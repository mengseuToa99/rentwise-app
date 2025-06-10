<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceMessage extends Model
{
    use HasFactory;

    protected $table = 'maintenance_messages';
    protected $primaryKey = 'message_id';

    protected $fillable = [
        'request_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Get the maintenance request this message belongs to
     */
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id', 'request_id');
    }

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    /**
     * Mark the message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Get the time elapsed since the message was sent
     */
    public function getTimeElapsedAttribute()
    {
        return $this->created_at->diffForHumans();
    }
} 