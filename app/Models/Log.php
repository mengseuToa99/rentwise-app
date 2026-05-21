<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_id',
        'subject_type',
        'ip_address',
        'user_agent',
        'changes',
        'timestamp',
    ];

    protected $casts = [
        'changes' => 'array',
        'timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public static function createLog(?int $userId, string $action, string $description, ?Model $subject = null): Log
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'subject_id' => $subject?->getKey(),
            'subject_type' => $subject ? get_class($subject) : null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
