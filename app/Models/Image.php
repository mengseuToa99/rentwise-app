<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'image_path',
        'caption',
        'is_primary',
        'sort_order',
        'uploaded_by_user_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id', 'user_id');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
