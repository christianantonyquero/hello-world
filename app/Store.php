<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class Store extends Model
{
    use SoftDeletes;

    public $guarded = ['id'];

    public $appends = ['admin_url', 'logo_url', 'photo_booth_bg_image_url', 'cart_bg_image_url'];

    protected $dates = [
        'open_at',
        'created_at',
    ];

    protected $casts = [
        'is_test_mode' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function pages()
    {
        return $this->hasMany(StorePage::class);
    }
    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class)->orderBy('name');
    }
    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('name');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function songs()
    {
        return $this->hasMany('App\StoreSong');
    }

    public function lineOpenings()
    {
        return $this->hasMany('App\StoreLineOpening')->orderBy('time', 'ASC');
    }

    public function getAdminUrlAttribute()
    {
        return route('admin.store.dashboard', [$this->id]);
    }

    public function getLogoUrlAttribute()
    {
        return ($this->logo) ? Storage::url($this->logo) : asset('img/logo-dark.svg');
    }

    public function getPhotoBoothBgImageUrlAttribute()
    {
        return $this->photo_booth_bg_image ? Storage::url($this->photo_booth_bg_image) : null;
    }
    
    public function getCartBgImageUrlAttribute()
    {
        return $this->cart_bg_image ? Storage::url($this->cart_bg_image) : null;
    }
    /**
     * Open/closed
     */
    public function isOpen()
    {
        return (!$this->open_at || $this->open_at < Carbon::now()) &&
               (!$this->close_at || $this->close_at > Carbon::now()) &&
               !$this->is_test_mode;
    }

    // Helper Factory
    public static function default_store() {
        return self::firstOrFail();
    }
}
