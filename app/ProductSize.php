<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSize extends Model
{
    use SoftDeletes;

    public $guarded = ['id'];

    public $casts = [
        "price" => "decimal:2",
        "display_order" => "integer",
        "is_available" => "boolean",
    ];

    protected static function boot()
    {
        parent::boot();
     
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('display_order', 'asc');
        });
    }
}
