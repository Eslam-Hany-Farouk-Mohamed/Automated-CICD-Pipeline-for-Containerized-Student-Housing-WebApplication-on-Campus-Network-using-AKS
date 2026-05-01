<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $chats = Chat::where('student_id', $user->id)
            ->orWhere('owner_id', $user->id)
            ->with(['student', 'owner', 'listing', 'latestMessage'])
            ->withCount([
                'unreadMessages' => function ($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($chats);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'listing_id' => 'nullable|exists:listings,id',
        ]);

        $user = $request->user();

        // Check if chat already exists
        $existingChat = Chat::where('student_id', $user->id)
            ->where('owner_id', $validated['owner_id'])
            ->where('listing_id', $validated['listing_id'] ?? null)
            ->first();

        if ($existingChat) {
            return response()->json([
                'chat' => $existingChat->load(['student', 'owner', 'listing', 'messages']),
            ]);
        }

        $chat = Chat::create([
            'student_id' => $user->id,
            'owner_id' => $validated['owner_id'],
            'listing_id' => $validated['listing_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Chat created successfully',
            'chat' => $chat->load(['student', 'owner', 'listing']),
        ], 201);
    }

    public function show(Request $request, Chat $chat)
    {
        $user = $request->user();

        if ($chat->student_id !== $user->id && $chat->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Mark messages as read
        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'chat' => $chat->load(['student', 'owner', 'listing', 'messages.sender']),
        ]);
    }

    public function destroy(Request $request, Chat $chat)
    {
        $user = $request->user();

        if ($chat->student_id !== $user->id && $chat->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $chat->delete();

        return response()->json([
            'message' => 'Chat deleted successfully',
        ]);
    }
}
