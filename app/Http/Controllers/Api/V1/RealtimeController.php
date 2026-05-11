<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class RealtimeController extends Controller
{
    public function heartbeat(Request $request)
    {
        $user = $request->user();
        $user->forceFill([
            'last_seen_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Heartbeat updated.',
        ]);
    }

    public function onlineUsers()
    {
        $users = User::query()
            ->select('id', 'name', 'email', 'last_seen_at')
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subSeconds(20))
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_seen_at' => $user->last_seen_at,
                    'status' => 'online',
                ];
            });

        return response()->json([
            'data' => $users,
        ]);
    }

    public function messages(Request $request)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = ChatMessage::query()
            ->with('user:id,name,email')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->format('H:i:s'),
                    'user' => [
                        'id' => $message->user?->id,
                        'name' => $message->user?->name,
                        'email' => $message->user?->email,
                    ],
                ];
            });

        return response()->json([
            'data' => $messages,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = ChatMessage::create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        $message->load('user:id,name,email');

        return response()->json([
            'message' => 'Pesan terkirim.',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at?->format('H:i:s'),
                'user' => [
                    'id' => $message->user?->id,
                    'name' => $message->user?->name,
                    'email' => $message->user?->email,
                ],
            ],
        ], 201);
    }
}