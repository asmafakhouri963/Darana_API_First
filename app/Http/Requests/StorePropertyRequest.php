<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Currency;
use App\Enums\PropertyType;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // التأكد أن المستخدم مسجل ولديه مكتب مرتبط وصلاحية إدخال
        return $this->user() && $this->user()->office()->exists();
    }

    public function rules(): array
    {
        return [
            'governorate_id' => ['required', 'exists:governorates,id'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'price'          => ['required', 'numeric', 'min:0'],
            'currency'       => ['required', 'string', 'in:' . implode(',', array_column(Currency::cases(), 'value'))],
            'type'           => ['required', 'string', 'in:' . implode(',', array_column(PropertyType::cases(), 'value'))],
            'commission_rate'=> ['nullable', 'numeric', 'min:0', 'max:100'],
            'internal_notes' => ['nullable', 'string'],
            'owner_phone'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'governorate_id.required' => 'اختيار المحافظة إلزامي.',
            'governorate_id.exists'   . 'المحافظة المحددة غير موجودة.',
            'title.required'          => 'عنوان العقار مطلوب.',
            'price.required'          => 'سعر العقار مطلوب.',
            'price.numeric'           => 'سعر العقار يجب أن يكون رقماً.',
        ];
    }

    public function attributes(): array
    {
        return [
            'governorate_id' => 'المحافظة',
            'title'          => 'عنوان العقار',
            'price'          => 'السعر',
        ];
    }
}