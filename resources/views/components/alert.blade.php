@props([
    'type' => 'success',
    'messages' => [],
])

@php
    $config = [
        'success' => [
            'bg' => 'bg-[var(--bg-success)]',
            'text' => 'text-[var(--text-success)]',
            'border' => 'border-[var(--border-success)]',
            'icon' => 'fa-circle-check',
        ],
        'warning' => [
            'bg' => 'bg-[var(--bg-warning)]',
            'text' => 'text-[var(--text-warning)]',
            'border' => 'border-[var(--border-warning)]',
            'icon' => 'fa-triangle-exclamation',
        ],
        'error' => [
            'bg' => 'bg-[var(--bg-error)]',
            'text' => 'text-[var(--text-error)]',
            'border' => 'border-[var(--border-error)]',
            'icon' => 'fa-circle-exclamation',
        ],
    ];
@endphp

@foreach ((array) $messages as $message)
    <div class="alert-message {{ $config[$type]['bg'] }} {{ $config[$type]['text'] }} {{ $config[$type]['border'] }} px-5 py-2 rounded-2xl flex items-center gap-2 fade-in">
        <i class='fas {{ $config[$type]['icon'] }} text-lg mr-1'></i>
        <p>{{ $message }}</p>
    </div>
@endforeach
