<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePhoto extends Model
{
    use HasFactory;

    protected $table = 'maintenance_photos';
    protected $primaryKey = 'photo_id';

    protected $fillable = [
        'request_id',
        'photo_path',
        'photo_type',
        'uploaded_by_type',
        'uploaded_by_id',
        'caption'
    ];

    /**
     * Get the maintenance request this photo belongs to
     */
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id', 'request_id');
    }

    /**
     * Get the user who uploaded the photo
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id', 'user_id');
    }

    /**
     * Get the full URL for the photo
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->photo_path);
    }
} 