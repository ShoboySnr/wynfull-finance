@props(['score' => 0])

@php
    $score = max(0, min(5, (int) $score)); // clamp 0..5
@endphp

<div class="knowledge-progress">
    <div class="knowledge-dots" aria-label="Knowledge score {{ $score }} of 5">
        @for ($i = 1; $i <= 5; $i++)
            <span class="dot {{ $i <= $score ? 'active' : '' }}" aria-hidden="true"></span>
        @endfor
        <span class="visually-hidden">{{ $score }} / 5</span>
    </div>
</div>

