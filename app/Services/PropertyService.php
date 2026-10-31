<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    protected PropertyCacheService $cacheService;

    public function __construct(PropertyCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getPropertyWithCache(int $id): ?Property
    {
        return $this->cacheService->getPropertyDetails($id);
    }

    public function updateProperty(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $property->update($data);
            
            // مسح وتحديث الكاش فوراً بعد التعديل لضمان عدم ظهور بيانات قديمة
            $this->cacheService->clearPropertyCache($property);

            return $property->fresh(['governorate', 'images']);
        });
    }
}