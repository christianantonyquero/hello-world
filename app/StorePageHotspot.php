<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorePageHotspot extends Model
{
    protected $fillable = [
        'store_page_id', 'type',
        'name', 'slug', 'value', 'display_order',
        'video', 'icon_url',
        'x', 'y'
    ];

    public static function HOTSPOT_TYPES() {
        return [
            'PAGE',
            'PRODUCT_CATEGORY',
            'PRODUCT',
            'HOME',
            'URL',
            'PHOTO-BOOTH',
        ];
    }

    // relationships
    public function page() {
        return $this->belongsTo('App\StorePage');
    }
}
