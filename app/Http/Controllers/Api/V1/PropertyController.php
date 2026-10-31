<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\V1\PropertyResource;
use App\Services\PropertyService;
use App\Services\PropertyCacheService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;
    protected PropertyCacheService $cacheService;

    public function __construct(PropertyService $propertyService, PropertyCacheService $cacheService)
    {
        $this->propertyService = $propertyService;
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        // استخدام Cursor Pagination لتطبيق الموبايل
        $properties = $this->propertyService->getPublicProperties($request->all());
        return PropertyResource::collection($properties);
    }

    public function show(int $id)
    {
        $property = $this->cacheService->getPropertyDetails($id);
        return new PropertyResource($property);
    }

    public function store(StorePropertyRequest $request)
    {
        $property = $this->propertyService->createProperty($request->validated(), $request->user()->office->id);
        return new PropertyResource($property);
    }

    public function update(UpdatePropertyRequest $request, $property)
    {
        $updated = $this->propertyService->updateProperty($property, $request->validated());
        return new PropertyResource($updated);
    }
}