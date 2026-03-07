<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PhotoBoothUserImage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'sharing_id', 'image',
        'caption', 'user_id', 'store_id',
    ];
    protected $appends = ['image_url', 'sharing_url'];

    protected $casts = [];

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    // Helpers
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getSharingUrlAttribute()
    {
        if( $this->sharing_id ) {
            return route('photo-booth.public-show', ['sharingID' => $this->sharing_id]);
        } else {
            return null;
        }
    }
}
