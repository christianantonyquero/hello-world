<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PhotoBoothBackdrop extends Model
{
    public static function WIDTH() {
        return 1080;
    }
    public static function HEIGHT() {
        return 1080;
    }

    protected $fillable = [
        'image', 'caption', 'display_order', 'store_id',
    ];

    protected $appends = ['image_url'];
    protected $casts = [];

    // Relationships
    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    // Helpers
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
