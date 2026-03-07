<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $appends = ['background_image_url', 'thumbnail_url'];

    public $guarded = ['id'];

    // helpers
    public function getBackgroundImageUrlAttribute()
    {
        return $this->background_image ? Storage::url($this->background_image) : null;
    }
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : null;
    }

    /**
     * Relationships
     */
    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_product_category')->withPivot('display_order')->orderBy('product_product_category.product_id');
    }

    public function store() 
    {
        return $this->belongsTo('App\Store');
    }

    public function store_page()
    {
        return $this->belongsTo('App\StorePage');
    }
}
