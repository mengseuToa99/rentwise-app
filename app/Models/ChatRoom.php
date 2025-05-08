<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'created_by'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_participants', 'chat_room_id', 'user_id')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function getUnreadCountAttribute()
    {
        return $this->messages()
            ->where('created_at', '>', $this->participants()
                ->where('chat_room_participants.user_id', auth()->id())
                ->first()
                ->pivot
                ->last_read_at ?? now()->subYears(10))
            ->count();
    }
}
