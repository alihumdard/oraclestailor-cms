@props(['status'])
@php
    $steps = ['confirmed', 'cutting', 'stitching', 'fitting', 'ready', 'delivered'];
    $labels = \App\Models\TailorOrder::STATUS_LABELS;
    $currentIndex = array_search($status, $steps, true);
@endphp
@if ($status === 'cancelled' || $status === 'new')
    <span class="status-badge status-{{ $status }}"><i></i>{{ $labels[$status] ?? $status }}</span>
@else
    <div class="order-progress">
        <div class="progress-track" role="list" aria-label="Order progress">
            @foreach ($steps as $i => $step)
                <span class="progress-step @if ($currentIndex !== false && $i <= $currentIndex) is-done @endif @if ($step === $status) is-current @endif" role="listitem" title="{{ $labels[$step] }}"><i></i></span>
            @endforeach
        </div>
        <span class="status-badge status-{{ $status }}"><i></i>{{ $labels[$status] ?? $status }}</span>
    </div>
@endif
