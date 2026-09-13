<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900" data-conversation-id="{{ $selectedConversation?->id }}">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Realtime Chat</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Your conversations</h1>
            </div>
            <p class="text-sm text-slate-600">Signed in as <span class="font-semibold text-slate-900">{{ request()->user()->name }}</span></p>
        </header>

        <main class="grid min-h-[36rem] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:grid-cols-[18rem_minmax(0,1fr)]">
        <aside class="border-b border-slate-200 bg-slate-50 p-5 lg:border-b-0 lg:border-r">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">My Rooms</h2>

            <form class="mt-4 space-y-3" method="POST" action="{{ route('conversations.store') }}">
                @csrf

                <label class="sr-only" for="room-name">Room name</label>
                <input class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" id="room-name" name="name" type="text" value="{{ old('name') }}" placeholder="Room name" required maxlength="255">
                <button class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2" type="submit">+ Create Room</button>

                @error('name')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </form>

            @if ($conversations->isEmpty())
                <p class="mt-6 rounded-lg border border-dashed border-slate-300 p-3 text-sm leading-6 text-slate-500">You do not have any rooms yet. Create one to begin chatting.</p>
            @else
                <ul class="mt-6 space-y-1">
                    @foreach ($conversations as $conversation)
                        <li>
                            <a class="block rounded-lg px-3 py-2 text-sm font-medium transition @if ($selectedConversation?->is($conversation)) bg-indigo-100 text-indigo-800 @else text-slate-700 hover:bg-slate-200 @endif" href="{{ route('conversations.show', $conversation) }}" @if ($selectedConversation?->is($conversation)) aria-current="page" @endif>
                                {{ $conversation->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>

        <section class="flex min-h-0 flex-col">
            @if ($selectedConversation)
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Selected Room</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-950">{{ $selectedConversation->name }}</h2>
                </div>

                <div class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-5 sm:p-6" id="messages" aria-live="polite">
                    @forelse ($messages as $message)
                        <div class="max-w-2xl rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <strong class="block text-sm text-indigo-700">{{ $message->user->name }}</strong>
                            <p class="mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-700">{{ $message->body }}</p>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500" id="empty-messages">No messages yet. Send the first message.</p>
                    @endforelse
                </div>

                <form class="border-t border-slate-200 bg-white p-4 sm:p-5" method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">

                    <label class="sr-only" for="message-body">Message</label>
                    <div class="flex gap-3">
                        <input class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" id="message-body" name="body" type="text" value="{{ old('body') }}" placeholder="Type your message..." required maxlength="1000">
                        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2" type="submit">Send</button>
                    </div>

                    @error('body')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    @error('conversation_id')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </form>
            @else
                <div class="flex flex-1 flex-col items-center justify-center p-8 text-center">
                    <div class="rounded-full bg-indigo-100 px-4 py-2 text-sm font-semibold text-indigo-700">Selected Room</div>
                    <h2 class="mt-4 text-xl font-bold text-slate-950">Choose a room to start chatting</h2>
                    <p class="mt-2 max-w-sm text-sm leading-6 text-slate-600">Select a room from the left, or create a new room for your conversation.</p>
                </div>
            @endif
        </section>
        </main>
    </div>
</body>
</html>
