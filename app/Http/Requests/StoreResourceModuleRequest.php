<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceModuleRequest extends FormRequest
{
    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'title'       => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'type'        => ['required', Rule::in(['file', 'template', 'word', 'pdf', 'excel', 'video'])],
            'file'      => [Rule::requiredIf($type !== 'video'),'file','max:102400'],
            'video_link'  => ['nullable', 'string','max:2048'],
            'status'      => ['nullable', Rule::in(['draft','pending'])],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
