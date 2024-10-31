<?php

namespace App\Models;

use App\Services\Polly;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;

    protected $guarded = [];

    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function saveSpeechFile()
    {
        $character = $this->chatSession->character;

        // Generate speech using the Polly service
        $speechPath = Polly::generateSpeech($this->content, 'speech_' . $this->id, $character->gender, $character->voice ?? null);

        $this->update([
            'speech_file_path' => $speechPath,
        ]);

        // Return the path to the saved audio file
        return $speechPath;
    }
}
