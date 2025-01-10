<?php

namespace App\Http\Controllers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;
use App\Models\ChatConversation;

class ChatController extends Controller
{
    public function chat()
    {
        return view('chat');
    }

    public function sendMessage(Request $request)
    {
        try {
            $userMessage = $request->input('message');

            $conversationHistory = ChatConversation::orderBy('conversationtime', 'asc')
                ->get(['user_message', 'chatgpt_response'])
                ->map(function ($message) {
                    return [
                        ['role' => 'user', 'content' => $message->user_message],
                        ['role' => 'assistant', 'content' => $message->chatgpt_response],
                    ];
                })
                ->flatten(1)
                ->toArray();

            $conversationHistory[] = ['role' => 'user', 'content' => $userMessage];

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => array_merge(
                    [['role' => 'system', 'content' => 'You are a helpful assistant.']],
                    $conversationHistory
                ),
            ]);

            if (isset($response['choices'][0]['message']['content'])) {
                $botMessage = $response['choices'][0]['message']['content'];

                ChatConversation::create([
                    'user_message' => $userMessage,
                    'chatgpt_response' => $botMessage,
                    'conversationtime' => now(),
                ]);

                return response()->json(['message' => $botMessage]);
            } else {
                return response()->json(['error' => 'Unexpected API response format.'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Chat API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
