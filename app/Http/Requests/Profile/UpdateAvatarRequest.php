<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => [
                'required','image','mimes:jpg,jpeg,png,webp,avif','max:2048', // 2MB
                //'dimensions:min_width=100,min_height=100'
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
