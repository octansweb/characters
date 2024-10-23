<?php

namespace App\Models;

use App\Models\Character;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatSession extends Model
{
    /** @use HasFactory<\Database\Factories\ChatSessionFactory> */
    use HasFactory;

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
