<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

test('can register a new character', function () {
    $requestData = [
        'name' => 'Test Character',
        'description' => 'A test character description',
        'personality' => 'Friendly and helpful',
        'is_public' => true,
        'gender' => 'Male',
        'voice' => 'Matthew',
        'device_name' => 'test_device'
    ];

    $response = $this->postJson('/api/register-character', $requestData);

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('token')
        );

    $this->assertDatabaseHas('users', [
        'id' => User::latest()->first()->id
    ]);

    $this->assertDatabaseHas('characters', [
        'name' => 'Test Character',
        'description' => 'A test character description',
        'personality' => 'Friendly and helpful',
        'is_public' => true,
        'gender' => 'Male',
        'voice' => 'Matthew'
    ]);
});

test('character registration validates required fields', function () {
    $response = $this->postJson('/api/register-character', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'is_public', 'gender', 'voice']);
});

test('can register character with avatar', function () {
    $avatar = UploadedFile::fake()->image('avatar.jpg');
    
    $requestData = [
        'name' => 'Character With Avatar',
        'avatar' => $avatar,
        'description' => 'A test character with avatar',
        'personality' => 'Creative',
        'is_public' => true,
        'gender' => 'Female',
        'voice' => 'Olivia',
        'device_name' => 'test_device'
    ];

    $response = $this->postJson('/api/register-character', $requestData);

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('token')
        );

    $this->assertDatabaseHas('characters', [
        'name' => 'Character With Avatar',
        'description' => 'A test character with avatar',
        'personality' => 'Creative',
        'is_public' => true,
        'gender' => 'Female',
        'voice' => 'Olivia'
    ]);
});

test('validates voice options', function () {
    $requestData = [
        'name' => 'Test Character',
        'description' => 'A test character',
        'personality' => 'Friendly',
        'is_public' => true,
        'gender' => 'Male',
        'voice' => 'InvalidVoice',
        'device_name' => 'test_device'
    ];

    $response = $this->postJson('/api/register-character', $requestData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['voice']);
});

test('creates new user with temporary fields and returns valid sanctum token', function () {
    $requestData = [
        'name' => 'Test Character',
        'description' => 'A test character',
        'is_public' => true,
        'gender' => 'Male',
        'voice' => 'Matthew',
        'device_name' => 'test_device'
    ];

    $response = $this->postJson('/api/register-character', $requestData);
    
    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('token')
        );

    // Get the latest user
    $user = User::latest()->first();
    
    // Assert user was created with temporary fields
    $this->assertNotNull($user);
    $this->assertNotEmpty($user->name);
    $this->assertNotEmpty($user->email);
    $this->assertNotEmpty($user->password);

    // Verify the token is valid and belongs to the user
    $token = $response->json('token');
    $this->assertNotEmpty($token);

    // Check if token exists in the database
    $personalAccessToken = PersonalAccessToken::findToken($token);
    $this->assertNotNull($personalAccessToken);
    $this->assertEquals($user->id, $personalAccessToken->tokenable_id);

    // Verify we can authenticate with the token
    $this->withToken($token)
        ->getJson('/api/user')
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $user->id)
                ->where('email', $user->email)
                ->etc()
        );
});
