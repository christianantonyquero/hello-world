<?php

namespace App;

// comment out age page use App\Traits\AgeGateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StorePage extends Model
{
   // comment out age gate use AgeGateTrait;

    protected $appends = ['background_image_url'];
    protected $fillable = [
        'store_id', 'background_image',
        'name', 'slug', 'minimum_age',
        'html_content', 'background_video',
        'intro_video', 'parent_id',
        'hide_from_menu', 'menu_display_order',
    ];

    protected $casts = [
        'hide_from_menu' => 'boolean'
    ];

    public function getRouteKeyName() {
        return 'slug';
    }

    /**
     * Relationships
     */
    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    public function hotspots()
    {
        return $this->hasMany(StorePageHotspot::class);
    }

    public function categories()
    {
        return $this->hasMany('App\ProductCategory');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    
    public function children() 
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name')->with('children');
    }

    // helpers
    public function getBackgroundImageUrlAttribute()
    {
        return $this->background_image ? Storage::url($this->background_image) : null;
    }
}
