<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#eef1ff] font-sans text-slate-900 selection:bg-violet-200" data-conversation-id="{{ $selectedConversation?->id }}">
    <div class="relative isolate min-h-screen overflow-hidden px-3 py-3 sm:px-6 sm:py-6 lg:px-10 lg:py-10">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-violet-300/70 via-indigo-100/50 to-transparent"></div>

        <main class="mx-auto grid min-h-[calc(100vh-1.5rem)] max-w-7xl overflow-hidden rounded-[2rem] border border-white/80 bg-white shadow-[0_30px_80px_-30px_rgba(55,48,163,0.38)] lg:min-h-[calc(100vh-5rem)] lg:grid-cols-[20rem_minmax(0,1fr)]">
        <aside class="relative flex flex-col overflow-hidden bg-gradient-to-b from-[#27205b] via-[#332a75] to-[#1d1949] p-5 text-white sm:p-6">
            <div class="pointer-events-none absolute -right-20 -top-20 h-48 w-48 rounded-full bg-fuchsia-400/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-16 h-48 w-48 rounded-full bg-cyan-300/20 blur-3xl"></div>

            <div class="relative flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-lg font-black text-violet-700 shadow-lg shadow-black/20">R</div>
                <div>
                    <p class="text-base font-bold tracking-tight">Ripple</p>
                    <p class="text-xs text-indigo-200">Realtime conversations</p>
                </div>
            </div>

            <div class="relative mt-9 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-200">Your rooms</h2>
                <span class="rounded-full bg-white/10 px-2.5 py-1 text-xs font-semibold text-indigo-100">{{ $conversations->count() }}</span>
            </div>

            <form class="relative mt-4 space-y-2" method="POST" action="{{ route('conversations.store') }}">
                @csrf

                <label class="sr-only" for="room-name">Room name</label>
                <input class="w-full rounded-xl border border-white/10 bg-white/10 px-3.5 py-2.5 text-sm text-white outline-none transition placeholder:text-indigo-200 focus:border-violet-300 focus:bg-white/15 focus:ring-2 focus:ring-violet-300/50" id="room-name" name="name" type="text" value="{{ old('name') }}" placeholder="Name a new room" required maxlength="255">
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-white px-3 py-2.5 text-sm font-bold text-violet-800 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-violet-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-violet-800" type="submit">
                    <span class="text-lg leading-none">+</span> Create room
                </button>

                @error('name')
                    <p class="text-sm text-rose-200">{{ $message }}</p>
                @enderror
            </form>

            @if ($conversations->isEmpty())
                <p class="relative mt-7 rounded-2xl border border-dashed border-white/20 bg-white/5 p-4 text-sm leading-6 text-indigo-100">Your conversations will appear here. Create the first room to begin.</p>
            @else
                <ul class="relative mt-7 space-y-1.5 overflow-y-auto pr-1">
                    @foreach ($conversations as $conversation)
                        <li>
                            <a class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition @if ($selectedConversation?->is($conversation)) bg-white text-violet-900 shadow-lg shadow-black/10 @else text-indigo-100 hover:bg-white/10 hover:text-white @endif" href="{{ route('conversations.show', $conversation) }}" @if ($selectedConversation?->is($conversation)) aria-current="page" @endif>
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-xs font-black @if ($selectedConversation?->is($conversation)) bg-violet-100 text-violet-700 @else bg-white/10 text-indigo-100 @endif">{{ strtoupper(substr($conversation->name, 0, 1)) }}</span>
                                <span class="min-w-0 truncate">{{ $conversation->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>

            <div class="relative mt-auto flex items-center gap-3 border-t border-white/10 pt-5">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-fuchsia-300 to-violet-300 text-sm font-extrabold text-violet-950">{{ strtoupper(substr(request()->user()->name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold">{{ request()->user()->name }}</p>
                    <p class="text-xs text-indigo-200">Online now</p>
                </div>
                <span class="ml-auto h-2.5 w-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-400/15"></span>
            </div>
        </aside>

        <section class="relative flex min-h-0 flex-col bg-[#fbfcff]">
            @if ($selectedConversation)
                <div class="flex items-center gap-4 border-b border-slate-100 bg-white px-5 py-4 sm:px-7 sm:py-5">
                    <div class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-700 text-sm font-black text-white shadow-lg shadow-violet-200">{{ strtoupper(substr($selectedConversation->name, 0, 1)) }}</div>
                    <div class="min-w-0">
                        <h2 class="truncate text-lg font-bold tracking-tight text-slate-950">{{ $selectedConversation->name }}</h2>
                        <p class="mt-0.5 flex items-center gap-1.5 text-xs font-medium text-slate-500"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Private room · Live messages</p>
                    </div>
                    <div class="ml-auto hidden rounded-full bg-violet-50 px-3 py-1.5 text-xs font-bold text-violet-700 sm:block">{{ $messages->count() }} {{ Str::plural('message', $messages->count()) }}</div>
                </div>

                <div class="relative flex-1 space-y-4 overflow-y-auto bg-[radial-gradient(circle_at_100%_0%,rgba(221,214,254,0.35),transparent_35%),linear-gradient(#fbfcff,#f7f8ff)] p-5 sm:p-7" id="messages" aria-live="polite">
                    <div class="mx-auto mb-7 w-fit rounded-full border border-slate-200/80 bg-white/80 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 shadow-sm">Conversation starts here</div>
                    @forelse ($messages as $message)
                        <div class="group flex max-w-2xl items-end gap-3">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-100 to-violet-100 text-xs font-extrabold text-violet-700">{{ strtoupper(substr($message->user->name, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <strong class="mb-1 block text-xs font-bold text-slate-500">{{ $message->user->name }}</strong>
                                <div class="rounded-2xl rounded-bl-md border border-slate-100 bg-white px-4 py-3 shadow-[0_8px_22px_-12px_rgba(30,41,59,0.25)]">
                                    <p class="whitespace-pre-wrap break-words text-sm leading-6 text-slate-700">{{ $message->body }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="mx-auto flex max-w-sm flex-col items-center rounded-3xl border border-dashed border-violet-200 bg-white/80 px-6 py-10 text-center shadow-sm" id="empty-messages">
                            <div class="grid h-14 w-14 place-items-center rounded-2xl bg-violet-100 text-2xl">✦</div>
                            <p class="mt-4 font-bold text-slate-800">No messages yet</p>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Break the ice and send the first message to this room.</p>
                        </div>
                    @endforelse
                </div>

                <form class="border-t border-slate-100 bg-white p-4 sm:p-5" method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">

                    <label class="sr-only" for="message-body">Message</label>
                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-1.5 transition focus-within:border-violet-300 focus-within:bg-white focus-within:ring-4 focus-within:ring-violet-100">
                        <input class="min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm outline-none placeholder:text-slate-400" id="message-body" name="body" type="text" value="{{ old('body') }}" placeholder="Write something thoughtful..." required maxlength="1000">
                        <button class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-violet-200 transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:ring-offset-2" type="submit">Send <span aria-hidden="true">↗</span></button>
                    </div>

                    @error('body')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    @error('conversation_id')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </form>
            @else
                <div class="relative flex flex-1 flex-col items-center justify-center overflow-hidden p-8 text-center">
                    <div class="absolute h-72 w-72 rounded-full bg-violet-200/30 blur-3xl"></div>
                    <div class="relative grid h-24 w-24 place-items-center rounded-[2rem] bg-gradient-to-br from-violet-600 to-indigo-700 text-4xl text-white shadow-2xl shadow-violet-300">✦</div>
                    <p class="relative mt-7 text-xs font-bold uppercase tracking-[0.18em] text-violet-600">Realtime Chat</p>
                    <h2 class="relative mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Pick a room. Start a ripple.</h2>
                    <p class="relative mt-3 max-w-sm text-sm leading-6 text-slate-500">Select a conversation from the sidebar, or create a new private room for your next idea.</p>
                </div>
            @endif
        </section>
        </main>
    </div>
</body>
</html>
