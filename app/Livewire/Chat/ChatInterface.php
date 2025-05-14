<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\ChatMessageSent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ChatInterface extends Component
{
    public $selectedRoom = null;
    public $message = '';
    public $rooms = [];
    public $messages = [];
    public $search = '';
    public $users = [];
    public $showUserList = false;
    public $onlineUsers;

    protected $listeners = [
        'echo:chat.*,ChatMessageSent' => 'handleBroadcastedMessage',
        'echo-presence:chat.*,here' => 'handlePresenceHere',
        'echo-presence:chat.*,joining' => 'handlePresenceJoining',
        'echo-presence:chat.*,leaving' => 'handlePresenceLeaving'
    ];

    public function mount()
    {
        $this->loadRooms();
        $this->loadUsers();
        $this->onlineUsers = collect();
    }

    public function loadRooms()
    {
        $this->rooms = ChatRoom::whereHas('participants', function ($query) {
            $query->where('chat_room_participants.user_id', Auth::id());
        })->with(['participants', 'messages' => function ($query) {
            $query->latest()->take(50);
        }])->get();
    }

    public function loadUsers()
    {
        $user = Auth::user();
        $query = User::query();

        // Filter users based on role
        if ($user->roles->contains('role_name', 'Admin')) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('role_name', ['Landlord', 'Tenant']);
            });
        } elseif ($user->roles->contains('role_name', 'Landlord')) {
            $query->whereHas('roles', function ($q) {
                $q->where('role_name', 'Tenant');
            });
        } elseif ($user->roles->contains('role_name', 'Tenant')) {
            $query->whereHas('roles', function ($q) {
                $q->where('role_name', 'Landlord');
            });
        }

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->users = $query->get();
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = ChatRoom::with(['participants', 'messages' => function ($query) {
            $query->latest()->take(50);
        }])->find($roomId);

        // Mark messages as read
        $this->selectedRoom->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Update last read timestamp
        $this->selectedRoom->participants()
            ->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);
            
        // Reset online users for new room
        $this->onlineUsers = collect();
    }

    public function createRoom($userId)
    {
        $otherUser = User::find($userId);
        
        // Check if room already exists
        $existingRoom = ChatRoom::whereHas('participants', function ($query) use ($userId) {
            $query->where('chat_room_participants.user_id', $userId);
        })->whereHas('participants', function ($query) {
            $query->where('chat_room_participants.user_id', Auth::id());
        })->first();

        if ($existingRoom) {
            $this->selectRoom($existingRoom->id);
            return;
        }

        // Create new room
        $room = ChatRoom::create([
            'type' => 'private',
            'created_by' => Auth::id()
        ]);

        // Add participants
        $room->participants()->attach([Auth::id(), $userId]);

        $this->loadRooms();
        $this->selectRoom($room->id);
        $this->showUserList = false;
    }

    public function sendMessage()
    {
        if (empty($this->message) || !$this->selectedRoom) {
            return;
        }

        $message = ChatMessage::create([
            'chat_room_id' => $this->selectedRoom->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'type' => 'text'
        ]);

        $this->message = '';
        $this->loadRooms();
        $this->selectRoom($this->selectedRoom->id);

        // Broadcast the message
        broadcast(new ChatMessageSent($message))->toOthers();
    }

    public function handleBroadcastedMessage($data)
    {
        if ($this->selectedRoom && $this->selectedRoom->id === $data['message']['chat_room_id']) {
            $this->loadRooms();
            $this->selectRoom($this->selectedRoom->id);
        } else {
            // Just reload rooms to show unread message indicators
            $this->loadRooms();
        }
    }
    
    public function handlePresenceHere($data) 
    {
        $this->onlineUsers = collect($data);
    }
    
    public function handlePresenceJoining($user) 
    {
        if (!$this->onlineUsers->contains('id', $user['id'])) {
            $this->onlineUsers->push($user);
        }
    }
    
    public function handlePresenceLeaving($user) 
    {
        $this->onlineUsers = $this->onlineUsers->filter(function ($u) use ($user) {
            return $u['id'] !== $user['id'];
        });
    }

    public function updated($property)
    {
        if ($property === 'search') {
            $this->loadUsers();
        }
    }

    public function render()
    {
        // If user is admin, use admin layout, otherwise use default
        $layout = 'components.layouts.app';
        if (Auth::user()->roles->contains('role_name', 'admin')) {
            $layout = 'layouts.admin';
        }
        
        return view('livewire.chat.chat-interface')
            ->layout($layout);
    }
}
