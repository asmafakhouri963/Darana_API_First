<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            // السعر كـ Object يضم المبلغ والعملة حسب متطلبات V2
            'price'       => [
                'amount'   => $this->price,
                'currency' => $this->currency->value,
            ],
            'status'      => $this->status->value,
            'type'        => $this->type->value,
            // إظهار رقم التواصل فقط للمستخدم المسجل دخول
            'owner_phone' => $this->when($request->user(), $this->owner_phone),
            'governorate' => $this->whenLoaded('governorate', fn() => $this->governorate->name),
            'images'      => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}