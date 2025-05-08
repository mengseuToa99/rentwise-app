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
        'timestamp'
    ];
    
    /**
     * Get the user that created the log entry
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    
    /**
     * Create a new log entry
     *
     * @param int $userId
     * @param string $action
     * @param string $description
     * @return Log
     */
    public static function createLog(int $userId, string $action, string $description): Log
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'timestamp' => now()
        ]);
    }
} 