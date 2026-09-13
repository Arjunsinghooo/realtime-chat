<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-conversation-id="{{ $selectedConversation?->id }}">
    <h1>Realtime Chat</h1>

    <p>Welcome, {{ request()->user()->name }}</p>

    <main style="display: grid; grid-template-columns: minmax(12rem, 1fr) minmax(20rem, 3fr); gap: 2rem; max-width: 60rem;">
        <aside>
            <h2>My Rooms</h2>

            <form method="POST" action="{{ route('conversations.store') }}">
                @csrf

                <label for="room-name">Room name</label>
                <input id="room-name" name="name" type="text" value="{{ old('name') }}" required maxlength="255">
                <button type="submit">Create Room</button>

                @error('name')
                    <p>{{ $message }}</p>
                @enderror
            </form>

            @if ($conversations->isEmpty())
                <p>You do not have any rooms yet.</p>
            @else
                <ul>
                    @foreach ($conversations as $conversation)
                        <li>
                            <a href="{{ route('conversations.show', $conversation) }}" @if ($selectedConversation?->is($conversation)) aria-current="page" @endif>
                                {{ $conversation->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>

        <section>
            @if ($selectedConversation)
                <h2>{{ $selectedConversation->name }}</h2>

                <div id="messages" aria-live="polite">
                    @forelse ($messages as $message)
                        <div>
                            <strong>{{ $message->user->name }}:</strong>
                            {{ $message->body }}
                        </div>
                    @empty
                        <p id="empty-messages">No messages yet.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">

                    <label for="message-body">Message</label>
                    <input id="message-body" name="body" type="text" value="{{ old('body') }}" placeholder="Type your message..." required maxlength="1000">
                    <button type="submit">Send</button>

                    @error('body')
                        <p>{{ $message }}</p>
                    @enderror
                    @error('conversation_id')
                        <p>{{ $message }}</p>
                    @enderror
                </form>
            @else
                <h2>Selected Room</h2>
                <p>Select a room to view its messages.</p>
            @endif
        </section>
    </main>
</body>
</html>
