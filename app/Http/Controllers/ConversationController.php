<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        return view('home', [
            'conversations' => $request->user()->conversations()->orderBy('name')->get(),
            'selectedConversation' => null,
            'messages' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $conversation = Conversation::create([
            'name' => $validated['name'],
        ]);

        $conversation->users()->attach($request->user()->id);

        return to_route('conversations.show', $conversation);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        Gate::authorize('view', $conversation);

        return view('home', [
            'conversations' => $request->user()->conversations()->orderBy('name')->get(),
            'selectedConversation' => $conversation,
            'messages' => $conversation->messages()->with('user')->oldest()->get(),
        ]);
    }
}
