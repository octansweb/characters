<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Character;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class CharacterChatController extends Controller
{
    public function show(Character $character, Request $request)
    {
        // Access Control
        if ($character->user_id !== $request->user()->id && !$character->is_public) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find or create the chat session
        $chatSession = ChatSession::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'character_id' => $character->id,
            ]
        );

        // Retrieve the conversation messages
        $conversation = $chatSession->messages()->orderBy('created_at')->get();

        // If it's a new chat session, initialize it
        if ($conversation->isEmpty()) {
            // Create system message
            $systemMessageContent = $character->personality ?? 'You are a helpful assistant.';
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'role' => 'system',
                'content' => $systemMessageContent,
            ]);
            // Refresh the conversation
            $conversation = $chatSession->messages()->orderBy('created_at')->get();
        }

        // Return JSON response
        return response()->json([
            'character' => $character,
            'conversation' => $conversation->where('role', '!=', 'system')->values(),
            'chatSessionId' => $chatSession->id,
        ], 200);
    }

    public function stream(Character $character, Request $request)
    {
        // Access Control
        if ($character->user_id !== $request->user()->id && !$character->is_public) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find or create the chat session
        $chatSession = ChatSession::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'character_id' => $character->id,
            ]
        );

        // Validate user's message
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Save user's message
        ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'role' => 'user',
            'content' => $request->input('message'),
        ]);

        // Retrieve messages for OpenAI API
        $messages = $chatSession->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                ];
            })
            ->toArray();

        try {
            // Call OpenAI API without streaming
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
            ]);

            $assistantContent = $response->choices[0]->message->content ?? '';

            // Save assistant's message
            $characterChatMessage = ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'role' => 'assistant',
                'content' => $assistantContent,
            ]);

            // Generate the speech file and update the message with the file path
            $speechPath = $characterChatMessage->saveSpeechFile();

            // Return the assistant's message
            return response()->json([
                'message' => $assistantContent,
                'speech_file_path' => $speechPath,
            ], 200);

        } catch (Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error communicating with the AI assistant.'], 500);
        }
    }
}
