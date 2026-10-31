<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'governorate_id',
        'title',
        'description',
        'price',
        'currency',
        'status',
        'type',
        'is_featured',
        'commission_rate',
        'internal_notes',
        'owner_phone',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => PropertyStatus::class,
        'type' => PropertyType::class,
        'currency' => Currency::class,
        'is_featured' => 'boolean',
    ];

    // علاقات
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Local Scopes للفلاتر السريعة
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', PropertyStatus::AVAILABLE);
    }
}