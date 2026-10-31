<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Cache;

class PropertyCacheService
{
    private const TTL = 3600; // ساعة واحدة كمدة صلاحية افتراضية

    /**
     * جلب تفاصيل العقار مع الكاش
     */
    public function getPropertyDetails(int $id): ?Property
    {
        return Cache::tags(['properties', "property_{$id}"])->remember("property_details_{$id}", self::TTL, function () use ($id) {
            return Property::with(['governorate', 'images', 'office'])->find($id);
        });
    }

    /**
     * جلب العقارات المميزة مع الكاش
     */
    public function getFeaturedProperties()
    {
        return Cache::tags(['properties', 'featured'])->remember('featured_properties', self::TTL, function () {
            return Property::with(['governorate', 'images'])->featured()->available()->take(10)->get();
        });
    }

    /**
     * جلب عدد العقارات المتاحة بكل محافظة مع الكاش
     */
    public function getCountByGovernorate(int $governorateId): int
    {
        return Cache::tags(['properties', "governorate_{$governorateId}"])->remember("gov_count_{$governorateId}", self::TTL, function () use ($governorateId) {
            return Property::where('governorate_id', $governorateId)->available()->count();
        });
    }

    /**
     * مسح وتحديث الكاش فوراً عند التعديل أو الحذف (Cache Invalidation)
     */
    public function clearPropertyCache(Property $property): void
    {
        // مسح كاش العقار المحدد وكاش المحافظة التابع لها فوراً دون تأخير
        Cache::tags([
            "property_{$property->id}",
            "governorate_{$property->governorate_id}",
            'featured',
            'properties'
        ])->flush();
    }
}