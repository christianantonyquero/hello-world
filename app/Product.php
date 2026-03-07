<?php

namespace App;

// DELETE AGE GATE Trait from PRoducts use App\Traits\AgeGateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

use App\ProductSize;

class Product extends Model
{
    use SoftDeletes; //, AgeGateTrait;

    public $guarded = ['id'];

    public $casts = [
        "price" => "decimal:2",
        "not_for_sale" => "boolean",
        "is_published" => "boolean",
        "is_suggested" => "boolean",
    ];

    protected $appends = [
        'thumbnail_url',
        'thumbnail_hover_url'
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
    public function scopeSuggested($query)
    {
        return $query->where('is_published', true)->where('is_suggested', true);
    }

    /**
     * Relationships
     */
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category')->withPivot('display_order');
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Add one or more existing categories to a product
     *
     * @param {Collection|array} $categories Collecttion of App\ProductCategory objects
     * @return void
     */
    public function addCategories($categories)
    {
        if (is_array($categories)) {
            $categories = collect($categories);
        }
        $this->categories()->attach($categories->pluck('id'));
    }

    /**
     * Add one or more sizes to a product (these are new App\ProductSize objects)
     *
     * @param {array} $sizes array of size objects in format ['size' => 'XL', 'price' => 3.33]
     * @return void
     */
    public function addSizes($sizes)
    {
        $this->sizes()->createMany($sizes);
    }

    /**
     * check whether product has a size with a given size string (XL, M, etc)
     *
     * @param {string} size string to check for
     * @return bool
     */
    public function hasSize($size)
    {
        return (bool) $this->sizes()->where('size', $size)->count();
    }

    /**
     * Add an image to this product
     *
     * @param {string} path of file (it assumes this is already uploaded)
     * @return ProductImage
     */
    public function addImage($path)
    {
        return $this->images()->create([
            'file' => $path,
        ]);
    }

    public function removeImages($ids)
    {
        return $this->images()->whereIn('id', $ids)->delete();
    }

    // Helpers
    public function getThumbnailUrlAttribute()
    {
        if($this->thumbnail) {
            return Storage::url($this->thumbnail);
        }
        // Thumbnanil is a newer field if we don't have one default to first image
        if($this->images->count()) {
            return $this->images->first()->url();
        }
        else {
            return url('img/product_placeholder.png');
        }
    }

    public function getThumbnailHoverUrlAttribute()
    {
        if($this->thumbnail_hover) {
            return Storage::url($this->thumbnail_hover);
        } else {
            return null;
        }
    }

    /**
     * Get Price
     *
     * @param {string|null} $size to look for, otherwise returns product price
     * @return float price
     */
    public function price(ProductSize $size = null)
    {
        if($size) {
            return $this->sizes()->find($size->id)->price;
        }
        return $this->price;
    }
}
