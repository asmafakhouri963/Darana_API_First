<?php

namespace App\Models\Concerns;

use App\Models\Category;

trait HasCategories
{
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }
}