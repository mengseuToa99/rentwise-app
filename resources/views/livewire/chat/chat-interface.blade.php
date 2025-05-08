<div class="flex h-[calc(100vh-4rem)]">
    <!-- Sidebar -->
    <div class="w-80 border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Search and New Chat -->
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <x-flux.input
                    wire:model.live="search"
                    placeholder="Search users..."
                    class="flex-1"
                />
                <x-flux.button
                    wire:click="$set('showUserList', true)"
                    icon="plus"
                    variant="outline"
                />
            </div>
        </div>

        <!-- Chat List -->
        <div class="overflow-y-auto h-[calc(100vh-8rem)]">
            @forelse($rooms as $room)
                <button
                    wire:click="selectRoom({{ $room->id }})"
                    class="w-full p-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ $selectedRoom?->id === $room->id ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
                >
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                                {{ $room->participants->where('user_id', '!=', auth()->id())->first()?->initials() }}
                            </span>
                            @if($room->unread_count > 0)
                                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                                    {{ $room->unread_count }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium">
                                {{ $room->participants->where('user_id', '!=', auth()->id())->first()?->name }}
                            </p>
                            <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $room->messages->last()?->message }}
                            </p>
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $room->messages->last()?->created_at?->diffForHumans() }}
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-4 text-center text-zinc-500 dark:text-zinc-400">
                    No conversations yet
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        @if($selectedRoom)
            <!-- Chat Header -->
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                        {{ $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->initials() }}
                    </span>
                    <div>
                        <h2 class="font-medium">
                            {{ $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->name }}
                        </h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $selectedRoom->participants->where('user_id', '!=', auth()->id())->first()?->roles->first()?->role_name }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages">
                @foreach($selectedRoom->messages->reverse() as $message)
                    <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-zinc-100 dark:bg-zinc-800' }} rounded-lg p-3">
                            <p>{{ $message->message }}</p>
                            <p class="text-xs {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-zinc-500 dark:text-zinc-400' }} mt-1">
                                {{ $message->created_at->format('g:i A') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                <form wire:submit="sendMessage" class="flex gap-2">
                    <x-flux.input
                        wire:model="message"
                        placeholder="Type a message..."
                        class="flex-1"
                    />
                    <x-flux.button
                        type="submit"
                        icon="paper-airplane"
                    />
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                Select a conversation to start chatting
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
                        class="w-full p-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg flex items-center gap-3"
                    >
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                            {{ $user->initials() }}
                        </span>
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
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
    });
</script>
@endscript
