<div x-data="{ chatView: null }" class="h-[calc(100vh-4rem)] bg-white dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Conversation List (shown by default on mobile, always visible on desktop) -->
    <div x-show="!chatView || window.innerWidth >= 768" class="w-full md:w-80 md:border-r border-zinc-200 dark:border-zinc-700 flex flex-col overflow-hidden">
        <!-- Header with search and new chat button -->
        <div class="p-2 sm:p-3 border-b border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row gap-2 sm:items-center">
            <h2 class="font-semibold text-gray-900 dark:text-white">Messages</h2>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <x-flux.input
                    wire:model.live="search"
                    placeholder="Search..."
                    class="w-full sm:w-40 text-sm"
                />
                <x-flux.button
                    wire:click="$set('showUserList', true)"
                    icon="plus"
                    variant="outline"
                    class="shrink-0"
                    size="sm"
                />
            </div>
        </div>

        <!-- Conversation List -->
        <div class="overflow-y-auto flex-1">
            @forelse($rooms as $room)
                <button
                    wire:click="selectRoom({{ $room->id }})"
                    @click="chatView = '{{ $room->id }}'"
                    class="w-full p-2 sm:p-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800 {{ $selectedRoom?->id === $room->id ? 'bg-zinc-100 dark:bg-zinc-800' : '' }} flex items-center"
                >
                    <div class="flex items-center gap-2 sm:gap-3 w-full">
                        <div class="relative flex-shrink-0">
                            <span class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700 text-sm">
                                {{ $room->participants->where('user_id', '!=', auth()->id())->first()?->initials() }}
                            </span>
                            @if($room->unread_count > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 sm:h-5 sm:w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                                    {{ $room->unread_count }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <div class="flex items-center justify-between w-full">
                                <p class="truncate font-medium text-gray-900 dark:text-white text-sm sm:text-base">
                                    {{ $room->participants->where('user_id', '!=', auth()->id())->first()?->name }}
                                </p>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1 sm:ml-2 whitespace-nowrap">
                                    {{ $room->messages->last()?->created_at?->diffForHumans(null, true) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="truncate text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 max-w-[12rem] sm:max-w-[180px]">
                                    {{ $room->messages->last()?->message }}
                                </p>
                                @php
                                    $isOnline = $onlineUsers->contains('id', $room->participants->where('user_id', '!=', auth()->id())->first()?->user_id ?? 0);
                                @endphp
                                @if($isOnline)
                                    <span class="h-2 w-2 rounded-full bg-green-500 ml-1 sm:ml-2 flex-shrink-0"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-4 text-center text-zinc-500 dark:text-zinc-400">
                    <p class="text-sm">No conversations yet</p>
                    <button 
                        wire:click="$set('showUserList', true)"
                        class="mt-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-blue-500 text-white rounded-md text-xs sm:text-sm font-medium hover:bg-blue-600"
                    >
                        Start a conversation
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div x-show="chatView || window.innerWidth >= 768" class="flex-1 flex flex-col h-full w-full">
        @if($selectedRoom)
            <!-- Chat Header -->
            <div class="p-2 sm:p-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center">
                <!-- Back button (mobile only) -->
                <button 
                    @click="chatView = null" 
                    class="md:hidden mr-2 flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700 mr-2 text-sm">
                    {{ $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->initials() }}
                </span>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                        <h2 class="font-medium text-gray-900 dark:text-white truncate text-sm sm:text-base">
                            {{ $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->name }}
                        </h2>
                        @php
                            $isOnline = $onlineUsers->contains('id', $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->user_id ?? 0);
                        @endphp
                        @if($isOnline)
                            <span class="h-2 w-2 rounded-full bg-green-500 ml-1 sm:ml-2"></span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1">Online</span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                        {{ $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->roles->first()?->role_name }}
                    </p>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-2 sm:p-3 space-y-2 sm:space-y-3" id="messages" wire:poll.10s>
                @foreach($selectedRoom->messages->sortBy('created_at') as $message)
                    <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] sm:max-w-[70%] {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-zinc-100 dark:bg-zinc-800' }} rounded-lg p-2 sm:p-3">
                            <p class="break-words text-sm">{{ $message->message }}</p>
                            <p class="text-[10px] sm:text-xs {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-zinc-500 dark:text-zinc-400' }} mt-1">
                                {{ $message->created_at->format('g:i A') }}
                                @if($message->is_read && $message->user_id === auth()->id())
                                    <span class="ml-1">✓</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="p-2 sm:p-3 border-t border-zinc-200 dark:border-zinc-700">
                <form wire:submit="sendMessage" class="flex gap-1 sm:gap-2">
                    <x-flux.input
                        wire:model="message"
                        placeholder="Type a message..."
                        class="flex-1 text-sm"
                        x-on:keydown.enter="$event.preventDefault(); $wire.sendMessage();"
                    />
                    <x-flux.button
                        type="submit"
                        icon="paper-airplane"
                        size="sm"
                        class="h-9 w-9 sm:h-10 sm:w-10 p-0 flex items-center justify-center"
                    />
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-zinc-500 dark:text-zinc-400 p-4 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12 mb-3 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-sm">Select a conversation to start chatting</p>
                <button 
                    @click="chatView = null" 
                    class="mt-3 sm:mt-4 px-3 py-1.5 sm:px-4 sm:py-2 bg-zinc-100 dark:bg-zinc-800 rounded-md text-xs sm:text-sm font-medium md:hidden"
                >
                    Back to conversations
                </button>
            </div>
        @endif
    </div>

    <!-- User List Modal -->
    <x-flux.modal wire:model="showUserList">
        <x-flux.modal.header>New Conversation</x-flux.modal.header>
        <x-flux.modal.content>
            <div class="space-y-2">
                @foreach($users as $user)
                    <button
                        wire:click="createRoom({{ $user->user_id }})"
                        @click="chatView = true"
                        class="w-full p-2 sm:p-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg flex items-center gap-2 sm:gap-3"
                    >
                        <span class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700 text-sm">
                            {{ $user->initials() }}
                        </span>
                        <div>
                            <div class="flex items-center gap-1 sm:gap-2">
                                <p class="font-medium text-sm sm:text-base">{{ $user->name }}</p>
                                @php
                                    $isOnline = $onlineUsers->contains('id', $user->user_id ?? 0);
                                @endphp
                                @if($isOnline)
                                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Online</span>
                                @endif
                            </div>
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $user->roles->first()?->role_name }}
                            </p>
                        </div>
                    </button>
                @endforeach
            </div>
        </x-flux.modal.content>
    </x-flux.modal>
</div>

@script
<script>
    // Set initial chat view state based on screen size and selected room
    document.addEventListener('DOMContentLoaded', () => {
        const selectedRoomId = @json($selectedRoom?->id);
        
        if (selectedRoomId && window.innerWidth < 768) {
            Alpine.store('chatView', selectedRoomId);
        }
        
        // Handle responsive behavior
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                // On desktop, show both panels
                Alpine.store('chatView', null);
            }
        });
    });
    
    // Scroll to bottom of messages
    function scrollToBottom() {
        const messages = document.getElementById('messages');
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    }

    // Scroll on initial load
    scrollToBottom();

    // Listen for new messages from handleBroadcastedMessage
    document.addEventListener('DOMContentLoaded', () => {
        Livewire.hook('message.processed', (message, component) => {
            scrollToBottom();
        });
        
        // Set up Echo presence channel
        if (window.Echo && window.Livewire) {
            const roomId = @json($selectedRoom?->id);
            if (roomId) {
                window.Echo.join(`chat.${roomId}`)
                    .here((users) => {
                        window.Livewire.dispatch('presence-here', users);
                    })
                    .joining((user) => {
                        window.Livewire.dispatch('presence-joining', user);
                    })
                    .leaving((user) => {
                        window.Livewire.dispatch('presence-leaving', user);
                    });
            }
        }
    });
</script>
@endscript
