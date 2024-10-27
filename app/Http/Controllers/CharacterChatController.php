<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Character;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CharacterChatController extends Controller
{
    public function show(Character $character, Request $request)
    {
        // Access Control
        if ($character->user_id !== $request->user()->id && !$character->is_public) {
            abort(403, 'Unauthorized action.');
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

            // Generate the assistant's first response (we'll handle this via streaming)
        }

        // Render the view
        return Inertia::render('Chat/Show', [
            'character' => $character,
            'conversation' => $conversation,
            'chatSessionId' => $chatSession->id,
        ]);
    }

    public function stream(Character $character, Request $request)
    {
        // Access Control
        if ($character->user_id !== $request->user()->id && !$character->is_public) {
            abort(403, 'Unauthorized action.');
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

            return response()->stream(function () use ($messages, $chatSession) {
                // Disable output buffering
                @ini_set('output_buffering', 'off');
                @ini_set('zlib.output_compression', 'off');
                @ini_set('implicit_flush', '1');
                for ($i = 0; $i < ob_get_level(); $i++) {
                    ob_end_flush();
                }
                ob_implicit_flush(1);
        
                // Set headers
                header('Content-Type: text/plain; charset=utf-8');
                header('Cache-Control: no-cache');
                header('X-Accel-Buffering: no'); // Disable buffering for nginx
        
                $assistantContent = '';
        
                try {
                    // Call OpenAI API with streaming
                    $openAIStream = OpenAI::chat()->createStreamed([
                        'model' => 'gpt-4o-mini', // Use a valid model
                        'messages' => $messages
                    ]);
        
                    foreach ($openAIStream as $response) {
                        $deltaContent = $response->choices[0]->delta->content ?? '';
        
                        if ($deltaContent !== '') {
                            $assistantContent .= $deltaContent;
        
                            // Output the content directly
                            echo $deltaContent;
        
                            // Flush the output
                            if (ob_get_level() > 0) {
                                ob_flush();
                            }
                            flush();
                        }
                    }
        
                    // After streaming is complete, save assistant's message
                    ChatMessage::create([
                        'chat_session_id' => $chatSession->id,
                        'role' => 'assistant',
                        'content' => $assistantContent,
                    ]);
        
                } catch (Exception $e) {
                    Log::error('OpenAI API Error: ' . $e->getMessage());
                    echo "Error communicating with the AI assistant.";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]);
        
    }
}
