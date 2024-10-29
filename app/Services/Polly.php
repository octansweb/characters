<?php

namespace App\Services;

use Aws\Polly\PollyClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Polly {
    public static function generateSpeech($text, $fileName = null, $gender = 'Male', $voiceId = null, $engine = 'generative')
    {
        Log::debug('polly:region', [
            'region' => env('AWS_DEFAULT_REGION'),
        ]);

        // Initialize Polly client
        $client = new PollyClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        // Determine the voice based on gender if no specific voiceId is provided
        if (is_null($voiceId)) {
            $voiceId = $gender === 'Male' 
                ? config('voices.defaultMale', 'Matthew') 
                : config('voices.defaultFemale', 'Joanna');
        }

        // Ensure the file name ends with .mp3
        $fileName = $fileName ?? uniqid('speech_', true) . '.mp3';
        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'mp3') {
            $fileName .= '.mp3';
        }

        try {
            // Call Polly's synthesizeSpeech method
            $result = $client->synthesizeSpeech([
                'OutputFormat' => 'mp3',
                'Text' => $text,
                'VoiceId' => $voiceId,
                'Engine' => $engine,
            ]);

            // Save the audio stream to a temporary file
            $tempFilePath = sys_get_temp_dir() . '/' . $fileName;
            file_put_contents($tempFilePath, $result['AudioStream']);

            // Store the file on the public disk and delete the temp file
            $storedFilePath = Storage::disk('public')->putFileAs('audio', $tempFilePath, $fileName);
            unlink($tempFilePath); // Remove the temporary file

            // Return the public URL to the stored file
            return Storage::disk('public')->url($storedFilePath);
        } catch (\Exception $e) {
            return "An error occurred: " . $e->getMessage();
        }
    }
}
