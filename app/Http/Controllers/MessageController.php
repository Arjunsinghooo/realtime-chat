<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $conversation = Conversation::findOrFail($validated['conversation_id']);

        Gate::authorize('sendMessage', $conversation);

        $message = Message::create([
            'user_id' => $request->user()->id,
            'conversation_id' => $conversation->id,
            'body' => $validated['body'],
        ]);

        MessageSent::dispatch($message);

        return to_route('conversations.show', $conversation);
    }
}
