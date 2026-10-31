<?php

namespace App\Models\Concerns;

use App\Models\Image;

trait HasImage
{
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}