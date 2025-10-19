<?php

namespace App\Http\Requests\Coach;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailabilityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'work_start_local' => ['required', 'date_format:H:i'],
            'work_end_local'   => ['required', 'date_format:H:i', 'different:work_start_local'],
            'timezone'         => ['required', 'string', 'timezone:all'],
            'session_duration_minutes' => [
                'required', 'integer', 'min:15', 'max:240',
                function ($attr, $value, $fail) {
                    if ($value % 5 !== 0) {
                        $fail('Session duration must be in 5-minute increments.');
                    }
                }
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $start = $this->input('work_start_local');
            $end   = $this->input('work_end_local');

            if ($start && $end && $start >= $end) {
                $v->errors()->add('work_end_local', 'End time must be after start time.');
            }
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}
