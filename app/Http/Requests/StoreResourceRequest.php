<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'string', Rule::in(['template', 'word', 'pdf', 'excel', 'video'])],
            'video_link' => ['nullable', 'required_if:type,video', 'url', 'max:255'],
            'file' => ['nullable', 'required_unless:type,video', 'file', 'mimes:doc,docx,pdf,xls,xlsx', 'max:10240'],
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['file'] = ['sometimes', 'nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'];
            $rules['video_link'] = ['sometimes', 'nullable', 'url', 'max:255'];
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
