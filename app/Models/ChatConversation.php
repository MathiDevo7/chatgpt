<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $table = 'conversation';

    protected $fillable = [
        'user_message',
        'chatgpt_response',
        'conversationtime',
    ];
}
