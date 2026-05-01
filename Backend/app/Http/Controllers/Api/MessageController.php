<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'content' => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $chat = Chat::findOrFail($validated['chat_id']);

        // Verify user is part of the chat
        if ($chat->student_id !== $user->id && $chat->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = $chat->messages()->create([
            'sender_id' => $request->has('system_sender_id') ? $request->system_sender_id : $user->id,
            'content' => $validated['content'],
        ]);

        // Update chat timestamp
        $chat->touch();

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load('sender'),
        ], 201);
    }

    public function markAsRead(Request $request, Chat $chat)
    {
        $user = $request->user();

        if ($chat->student_id !== $user->id && $chat->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Messages marked as read',
        ]);
    }
}
