<?php

namespace App\Http\Requests\Client;

use App\Models\CoachAvailabilitySetting;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookSessionRequest extends FormRequest
{
//    public function rules(): array
//    {
//        return [
//            'coach_id'    => ['required','integer','exists:users,id'],
//            'date'        => ['required','date_format:Y-m-d'],
//            'start_time'  => ['required','date_format:H:i'], // in coach tz
//            'title'       => ['nullable','string','max:120'],
//            'type'        => ['nullable','string','max:50'],
//            'notes'       => ['nullable','string','max:1000'],
//            'location_url'=> ['nullable','url','max:255'],
//        ];
//    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Accept either:
     *  - ISO8601 starts_at (UTC)  -> will be converted to date + start_time in coach tz
     *  - OR date (Y-m-d) + start_time (H:i) directly
     */
    public function rules(): array
    {
        return [
            'coach_id'     => ['required', 'integer', 'exists:users,id'],

            // Path A: direct local parts
            'date'         => ['nullable', 'date_format:Y-m-d'],
            'start_time'   => ['nullable', 'date_format:H:i'],

            // Path B: ISO input
            'starts_at'    => ['nullable', 'date'],     // ISO8601 like 2025-10-31T15:55:00.000Z
            'ends_at'      => ['nullable', 'date'],     // optional; we’ll ignore or recompute

            'title'        => ['nullable', 'string', 'max:120'],
            'type'         => ['nullable', 'string', 'max:50'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'location_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Normalize inputs BEFORE validation runs:
     * - If starts_at is present, compute date + start_time in coach's timezone.
     * - If ends_at missing, optionally compute one using coach session duration (not strictly required).
     */
    protected function prepareForValidation(): void
    {
        $input    = $this->all();
        $coachId  = (int) ($input['coach_id'] ?? 0);

        // Try to get coach tz & session duration; fall back gracefully
        $tz       = config('app.timezone', 'UTC');
        $duration = null;

        if ($coachId > 0) {
            $avail = CoachAvailabilitySetting::query()->where('coach_id', $coachId)->first();
            if ($avail) {
                $tz       = $avail->timezone ?: $tz;
                $duration = (int) $avail->session_duration_minutes ?: null;
            }
        }

        // If starts_at provided (typically ISO UTC), derive date + start_time in coach tz
        if (!empty($input['starts_at'])) {
            try {
                $startUtc   = CarbonImmutable::parse($input['starts_at']); // assumes UTC if trailing Z
                $startLocal = $startUtc->setTimezone($tz);

                $this->merge([
                    'date'       => $input['date']       ?? $startLocal->toDateString(),         // YYYY-MM-DD
                    'start_time' => $input['start_time'] ?? $startLocal->format('H:i'),          // HH:MM
                ]);

                // If ends_at is missing and duration is known, compute a helpful ends_at (UTC)
                if (empty($input['ends_at']) && $duration) {
                    $endUtc = $startLocal->addMinutes($duration)->utc();
                    $this->merge(['ends_at' => $endUtc->toIso8601String()]);
                }
            } catch (\Throwable $e) {
                // If parse fails, let validator handle via the 'date' rule later
            }
        }

        // Trim a few strings
        foreach (['title','type','notes','location_url'] as $field) {
            if (isset($input[$field]) && is_string($input[$field])) {
                $this->merge([$field => trim($input[$field])]);
            }
        }
    }

    /**
     * Ensure we have either starts_at OR (date AND start_time) after normalization.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $hasIso  = (bool) $this->input('starts_at');
            $hasPair = (bool) ($this->input('date') && $this->input('start_time'));

            if (!$hasIso && !$hasPair) {
                $v->errors()->add('starts_at', 'Provide either starts_at (ISO8601) or date + start_time.');
                $v->errors()->add('date',      'Provide either starts_at (ISO8601) or date + start_time.');
                $v->errors()->add('start_time','Provide either starts_at (ISO8601) or date + start_time.');
            }
        });
    }
}
