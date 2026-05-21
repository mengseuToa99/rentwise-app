<div x-data="{ chatView: null }" class="flex h-[calc(100vh-4rem)] flex-col bg-stone-50 dark:bg-zinc-950 md:flex-row">

    {{-- ============================================================ --}}
    {{-- LEFT RAIL — conversation list                                --}}
    {{-- ============================================================ --}}
    <aside
        x-show="!chatView || window.innerWidth >= 768"
        class="flex w-full flex-col overflow-hidden border-r border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 md:w-80 lg:w-96"
    >
        {{-- Header --}}
        <div class="border-b border-gray-200 px-4 py-3 dark:border-zinc-800">
            <div class="mb-3 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Messages</h1>
                <button
                    type="button"
                    wire:click="$set('showUserList', true)"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                    title="New conversation"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>

            {{-- Search pill --}}
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 110-16 8 8 0 010 16z"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.250ms="search"
                    placeholder="Search conversations"
                    class="block w-full rounded-full border border-transparent bg-gray-100 py-2 pl-9 pr-3 text-sm placeholder:text-gray-500 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-zinc-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:bg-zinc-800"
                >
            </div>
        </div>

        {{-- List --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($rooms as $room)
                @php
                    $other = $room->participants->where('user_id', '!=', auth()->id())->first();
                    $isSelected = $selectedRoom?->id === $room->id;
                    $isOnline = $other && $onlineUsers->contains('id', $other->user_id);
                    $lastMessage = $room->messages->last();
                    $isOwnLast = $lastMessage && $lastMessage->user_id === auth()->id();
                @endphp
                <button
                    type="button"
                    wire:click="selectRoom({{ $room->id }})"
                    @click="chatView = '{{ $room->id }}'"
                    class="flex w-full items-start gap-3 px-4 py-3 text-left transition {{ $isSelected ? 'bg-blue-50 dark:bg-blue-950/30' : 'hover:bg-gray-50 dark:hover:bg-zinc-800/60' }} border-b border-gray-100 dark:border-zinc-800/60"
                >
                    {{-- Avatar with presence dot --}}
                    <div class="relative shrink-0">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-sm font-semibold text-white shadow-sm">
                            {{ $other?->initials() ?? '?' }}
                        </span>
                        @if($isOnline)
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $other?->name ?? 'Unknown' }}
                            </p>
                            @if($lastMessage)
                                <span class="shrink-0 text-[11px] font-medium {{ $room->unread_count > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $lastMessage->created_at->diffForHumans(null, true) }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-0.5 flex items-center justify-between gap-2">
                            <p class="truncate text-sm {{ $room->unread_count > 0 ? 'font-semibold text-gray-700 dark:text-gray-200' : 'text-gray-500 dark:text-gray-400' }}">
                                @if($isOwnLast)<span class="text-gray-400 dark:text-gray-500">You: </span>@endif{{ $lastMessage?->message ?? 'No messages yet' }}
                            </p>
                            @if($room->unread_count > 0)
                                <span class="flex h-5 min-w-[1.25rem] shrink-0 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[11px] font-bold text-white">
                                    {{ $room->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No conversations yet</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Start one to break the ice.</p>
                    <button
                        type="button"
                        wire:click="$set('showUserList', true)"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        New chat
                    </button>
                </div>
            @endforelse
        </div>
    </aside>

    {{-- ============================================================ --}}
    {{-- RIGHT PANE — conversation                                    --}}
    {{-- ============================================================ --}}
    <section x-show="chatView || window.innerWidth >= 768" class="flex h-full w-full flex-1 flex-col">
        @if($selectedRoom)
            @php
                $partner = $selectedRoom->participants->where('user_id', '!=', auth()->id())->first();
                $partnerOnline = $partner && $onlineUsers->contains('id', $partner->user_id);
            @endphp

            {{-- Chat header --}}
            <header class="flex items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900">
                <button
                    type="button"
                    @click="chatView = null"
                    class="md:hidden flex h-9 w-9 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-zinc-800"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="relative shrink-0">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-sm font-semibold text-white shadow-sm">
                        {{ $partner?->initials() ?? '?' }}
                    </span>
                    @if($partnerOnline)
                        <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-base font-semibold text-gray-900 dark:text-white">
                        {{ $partner?->name ?? 'Unknown' }}
                    </h2>
                    <p class="truncate text-xs {{ $partnerOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}">
                        @if($partnerOnline)
                            Online
                        @else
                            {{ ucfirst($partner?->roles->first()?->role_name ?? 'Offline') }}
                        @endif
                    </p>
                </div>
            </header>

            {{-- Messages --}}
            <div
                id="messages"
                wire:poll.10s
                class="flex-1 overflow-y-auto px-3 py-4 sm:px-6"
                style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.04) 1px, transparent 0); background-size: 22px 22px;"
            >
                @php
                    $sorted = $selectedRoom->messages->sortBy('created_at')->values();
                    $lastDate = null;
                    $prevSender = null;
                @endphp

                <div class="mx-auto flex max-w-3xl flex-col gap-1">
                    @foreach($sorted as $i => $message)
                        @php
                            $isMine = $message->user_id === auth()->id();
                            $dateKey = $message->created_at->format('Y-m-d');
                            $showDate = $dateKey !== $lastDate;
                            $sameSenderAsPrev = !$showDate && $prevSender === $message->user_id;
                            $next = $sorted[$i + 1] ?? null;
                            $isLastInGroup = !$next || $next->user_id !== $message->user_id || $next->created_at->format('Y-m-d') !== $dateKey;
                            $lastDate = $dateKey;
                            $prevSender = $message->user_id;
                        @endphp

                        @if($showDate)
                            <div class="my-3 flex items-center justify-center">
                                <span class="rounded-full bg-gray-200/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-gray-600 dark:bg-zinc-800 dark:text-gray-300">
                                    @if($message->created_at->isToday()) Today
                                    @elseif($message->created_at->isYesterday()) Yesterday
                                    @else {{ $message->created_at->format('D, d M Y') }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} {{ $sameSenderAsPrev ? 'mt-0.5' : 'mt-2' }}">
                            <div
                                @class([
                                    'max-w-[80%] sm:max-w-[65%] px-3.5 py-2 shadow-sm',
                                    'bg-blue-600 text-white' => $isMine,
                                    'bg-white text-gray-900 dark:bg-zinc-800 dark:text-gray-100 border border-gray-100 dark:border-zinc-700/60' => !$isMine,
                                    // Bubble shape — grouped messages share rounded edges
                                    'rounded-2xl rounded-br-md' => $isMine && !$isLastInGroup,
                                    'rounded-2xl rounded-br-sm' => $isMine && $isLastInGroup,
                                    'rounded-2xl rounded-bl-md' => !$isMine && !$isLastInGroup,
                                    'rounded-2xl rounded-bl-sm' => !$isMine && $isLastInGroup,
                                ])
                            >
                                <p class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $message->message }}</p>
                                @if($isLastInGroup)
                                    <div class="mt-1 flex items-center justify-end gap-1">
                                        <span class="text-[10px] {{ $isMine ? 'text-blue-100/90' : 'text-gray-400 dark:text-gray-500' }}">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                        @if($isMine)
                                            @if($message->is_read)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M1 13l4 4L13 8M9 13l4 4L21 8"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-200/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($sorted->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Say hi to start the conversation.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Composer --}}
            <div class="border-t border-gray-200 bg-white px-3 py-3 dark:border-zinc-800 dark:bg-zinc-900 sm:px-4">
                <form wire:submit="sendMessage" class="mx-auto flex max-w-3xl items-end gap-2">
                    <div class="flex flex-1 items-end rounded-2xl border border-gray-200 bg-gray-50 px-3 py-2 focus-within:border-blue-500 focus-within:bg-white focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:focus-within:bg-zinc-800">
                        <textarea
                            wire:model="message"
                            placeholder="Type a message…"
                            rows="1"
                            x-data="{
                                autosize() {
                                    this.$el.style.height = 'auto';
                                    this.$el.style.height = Math.min(this.$el.scrollHeight, 160) + 'px';
                                }
                            }"
                            x-init="autosize()"
                            x-on:input="autosize()"
                            x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.sendMessage(); $el.style.height = 'auto'; }"
                            class="block w-full resize-none border-0 bg-transparent p-0 text-sm leading-relaxed text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-0 dark:text-white dark:placeholder:text-gray-400"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @disabled(trim($message) === '')
                        wire:loading.attr="disabled"
                    >
                        <svg wire:loading.remove wire:target="sendMessage" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                        <svg wire:loading wire:target="sendMessage" class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                    </button>
                </form>
            </div>
        @else
            {{-- No conversation selected --}}
            <div class="flex flex-1 flex-col items-center justify-center px-6 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 dark:from-indigo-950/40 dark:to-blue-950/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your messages</h3>
                <p class="mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">Pick a conversation from the left to read messages, or start a new one.</p>
                <button
                    type="button"
                    @click="chatView = null"
                    class="mt-4 inline-flex items-center gap-1.5 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800 md:hidden"
                >
                    Back to conversations
                </button>
            </div>
        @endif
    </section>

    {{-- ============================================================ --}}
    {{-- NEW CONVERSATION MODAL                                       --}}
    {{-- ============================================================ --}}
    @if($showUserList)
        <div
            x-data="{ q: '' }"
            x-on:keydown.escape.window="$wire.set('showUserList', false)"
            class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
        >
            {{-- Backdrop --}}
            <div
                wire:click="$set('showUserList', false)"
                x-transition.opacity.duration.150ms
                class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
            ></div>

            {{-- Panel --}}
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative w-full overflow-hidden rounded-t-3xl bg-white shadow-2xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10 sm:max-w-md sm:rounded-3xl"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-zinc-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">New conversation</h3>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Pick someone to message.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showUserList', false)"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                        aria-label="Close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search --}}
                <div class="px-5 pt-4">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 110-16 8 8 0 010 16z"/></svg>
                        <input
                            type="text"
                            x-model="q"
                            placeholder="Search by name or role"
                            autofocus
                            class="block w-full rounded-full border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-3 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:bg-zinc-800"
                        >
                    </div>
                </div>

                {{-- List --}}
                <div class="max-h-[60vh] overflow-y-auto px-3 py-3">
                    @if(count($users) === 0)
                        <div class="px-2 py-10 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No users available</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add someone first, then come back.</p>
                        </div>
                    @else
                        <div x-data="{ visible: 0 }" x-init="$nextTick(() => visible = $root.querySelectorAll('[data-user-row]:not(.hidden)').length)" class="space-y-1">
                            @foreach($users as $user)
                                @php
                                    $userOnline = $onlineUsers->contains('id', $user->user_id);
                                    $roleName = ucfirst($user->roles->first()?->role_name ?? 'User');
                                    $searchHaystack = strtolower(trim(($user->name ?? '') . ' ' . $roleName));
                                @endphp
                                <button
                                    type="button"
                                    data-user-row
                                    x-show="q === '' || @js($searchHaystack).includes(q.toLowerCase())"
                                    wire:click="createRoom({{ $user->user_id }})"
                                    @click="chatView = true"
                                    class="group flex w-full items-center gap-3 rounded-2xl p-2.5 text-left transition hover:bg-blue-50 dark:hover:bg-blue-950/30"
                                >
                                    <div class="relative shrink-0">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-sm font-semibold text-white shadow-sm">
                                            {{ $user->initials() }}
                                        </span>
                                        @if($userOnline)
                                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="truncate text-xs {{ $userOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            @if($userOnline) Online @else {{ $roleName }} @endif
                                        </p>
                                    </div>
                                    <span class="hidden rounded-full bg-gray-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gray-600 dark:bg-zinc-800 dark:text-gray-300 sm:inline">
                                        {{ $roleName }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-blue-500 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @endforeach

                            {{-- Client-side "no match" shown when search hides all rows --}}
                            <div
                                x-show="q !== '' && $root.querySelectorAll('[data-user-row]:not([style*=\'display: none\'])').length === 0"
                                class="px-2 py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                No one matches "<span x-text="q"></span>".
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    function scrollChatToBottom() {
        const el = document.getElementById('messages');
        if (el) el.scrollTop = el.scrollHeight;
    }

    document.addEventListener('DOMContentLoaded', () => {
        scrollChatToBottom();

        // Re-scroll after Livewire updates
        if (window.Livewire) {
            Livewire.hook('message.processed', () => scrollChatToBottom());
        }

        // Presence channel
        if (window.Echo && window.Livewire) {
            const roomId = @json($selectedRoom?->id);
            if (roomId) {
                window.Echo.join(`chat.${roomId}`)
                    .here((users) => window.Livewire.dispatch('presence-here', users))
                    .joining((user) => window.Livewire.dispatch('presence-joining', user))
                    .leaving((user) => window.Livewire.dispatch('presence-leaving', user));
            }
        }
    });
</script>
@endscript
