<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    /**
     * جلب القوائم للوحة التحكم باستخدام Offset Pagination التقليدي
     */
    public function getOfficeProperties(int $officeId, int $perPage = 15): LengthAwarePaginator
    {
        return Property::where('office_id', $officeId)
            ->with(['governorate', 'images'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * جلب القوائم لتطبيق الموبايل باستخدام Cursor Pagination (لتحنب التكرار والأداء العالي)
     */
    public function getPublicProperties(array $filters, int $perPage = 15): CursorPaginator
    {
        $query = Property::query()
            ->with(['governorate', 'images']) // منع مشكلة N+1
            ->when(isset($filters['governorate_id']), function ($q) use ($filters) {
                $q->where('governorate_id', $filters['governorate_id']);
            })
            ->when(isset($filters['type']), function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            })
            ->latest();

        return $query->cursorPaginate($perPage);
    }

    /**
     * إنشاء عقار جديد
     */
    public function createProperty(array $data, int $officeId): Property
    {
        return DB::transaction(function () use ($data, $officeId) {
            $data['office_id'] = $officeId;
            return Property::create($data);
        });
    }

    /**
     * تحديث عقار
     */
    public function updateProperty(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $property->update($data);
            return $property->fresh(['governorate', 'images']);
        });
    }
}