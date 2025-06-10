<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaintenanceRequest extends Model
{
    use HasFactory;
    
    protected $table = 'maintenance_requests';
    protected $primaryKey = 'request_id';
    
    protected $fillable = [
        'tenant_id',
        'property_id',
        'room_id',
        'title',
        'description',
        'priority',
        'status',
        'landlord_notes',
        'completed_at'
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Urgent keywords for priority detection
    protected static $urgentKeywords = [
        'leak', 'flood', 'water damage', 'electrical', 'fire', 'smoke',
        'no heat', 'no water', 'no electricity', 'gas', 'broken pipe',
        'sewage', 'emergency', 'dangerous', 'safety', 'hazard'
    ];

    // High priority keywords
    protected static $highPriorityKeywords = [
        'broken', 'not working', 'damaged', 'stuck', 'issues',
        'problem', 'repair', 'fix', 'malfunction'
    ];
    
    /**
     * Get the tenant that made the maintenance request
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }
    
    /**
     * Get the property associated with the maintenance request
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
    
    /**
     * Get the room associated with the maintenance request
     */
    public function room()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }

    /**
     * Get the photos associated with the maintenance request
     */
    public function photos()
    {
        return $this->hasMany(MaintenancePhoto::class, 'request_id', 'request_id');
    }

    /**
     * Get the messages associated with the maintenance request
     */
    public function messages()
    {
        return $this->hasMany(MaintenanceMessage::class, 'request_id', 'request_id');
    }
    
    /**
     * Get the landlord through the property relationship
     */
    public function landlord()
    {
        return $this->property->landlord();
    }

    /**
     * Auto-detect priority based on description
     */
    public static function detectPriority($title, $description)
    {
        $text = strtolower($title . ' ' . $description);
        
        // Check for urgent keywords
        foreach (self::$urgentKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return 'urgent';
            }
        }
        
        // Check for high priority keywords
        foreach (self::$highPriorityKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return 'high';
            }
        }
        
        return 'medium';
    }

    /**
     * Get the average resolution time for completed requests
     */
    public static function getAverageResolutionTime()
    {
        $completedRequests = self::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedRequests->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        foreach ($completedRequests as $request) {
            $totalHours += $request->created_at->diffInHours($request->completed_at);
        }

        return round($totalHours / $completedRequests->count(), 1);
    }
    
    /**
     * Scope a query to only include maintenance requests with a specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include maintenance requests for a specific tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
    
    /**
     * Scope a query to only include maintenance requests for properties owned by a specific landlord
     */
    public function scopeForLandlord($query, $landlordId)
    {
        return $query->whereHas('property', function ($q) use ($landlordId) {
            $q->where('landlord_id', $landlordId);
        });
    }

    /**
     * Scope a query to only include urgent requests
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Get the time elapsed since the request was created
     */
    public function getTimeElapsedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get whether the request is overdue (no response within 24 hours for urgent, 48 for high, 72 for others)
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'completed' || $this->status === 'rejected') {
            return false;
        }

        $hours = $this->created_at->diffInHours(now());
        
        return match($this->priority) {
            'urgent' => $hours > 24,
            'high' => $hours > 48,
            default => $hours > 72
        };
    }
} 