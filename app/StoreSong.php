<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoreSong extends Model
{
    protected $appends = ['track_artwork_url'];
    protected $fillable = [
        'track_name', 'track_artwork', 'track_audio_file',
        'artist_name', 'album_name', 'track_link', 
        'play_order', 'store_id'
    ];

    public function store() {
        return $this->belongsTo('App\Store');
    }

    // helpers
    public function getTrackArtworkUrlAttribute()
    {
        return $this->track_artwork ? Storage::url($this->track_artwork) : null;
    }
}
