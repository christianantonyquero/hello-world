<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use SoftDeletes;

    public $guarded = ['id'];

    protected $appends = ['file_url'];

    public function url()
    {
        return $this->file ? Storage::url($this->file) : null;
    }

    public function getFileUrlAttribute()
    {
        return $this->url();
    }
}
