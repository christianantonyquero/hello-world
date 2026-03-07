<?php

namespace App\ImageTemplates;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class FacebookShare implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(800, 800);
    }
}
