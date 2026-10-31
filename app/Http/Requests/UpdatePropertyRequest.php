<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');
        
        // التأكد أن المكتب الحالي يمتلك هذا العقار حصراً ولا يمتلك عقارات غيره[cite: 5, 6]
        return $this->user() && 
               $this->user()->office()->exists() && 
               $property->office_id === $this->user()->office->id;
    }

    public function rules(): array
    {
        return [
            'title'          => ['sometimes', 'required', 'string', 'max:255'],
            'description'    => ['sometimes', 'required', 'string'],
            'price'          => ['sometimes', 'required', 'numeric', 'min:0'],
            'status'         => ['sometimes', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان العقار مطلوب عند التعديل.',
        ];
    }
}