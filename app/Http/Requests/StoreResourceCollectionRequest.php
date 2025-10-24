<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceCollectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'icon_class' => ['nullable','string','max:100'],
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
