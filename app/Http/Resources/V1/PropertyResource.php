<?php

namespace App\Http\Resources\V1;

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
            'price'       => $this->price, // السعر رقم مجرد في النسخة الأولى
            'status'      => $this->status->value,
            'type'        => $this->type->value,
            'governorate' => $this->whenLoaded('governorate', fn() => $this->governorate->name),
            'images'      => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}