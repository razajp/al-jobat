@props([
    'type' => 'info',
    'messages' => [],
])

@php
    $config = [
        'info' => [
            'bg' => 'bg-[var(--secondary-bg-color)]',
            'text' => 'text-[var(--secondary-text)]',
            'icon' => 'fa-circle-info',
        ],
        'success' => [
            'bg' => 'bg-[var(--bg-success)]',
            'text' => 'text-[var(--text-success)]',
            'icon' => 'fa-circle-check',
        ],
        'warning' => [
            'bg' => 'bg-[var(--bg-warning)]',
            'text' => 'text-[var(--text-warning)]',
            'icon' => 'fa-triangle-exclamation',
        ],
        'error' => [
            'bg' => 'bg-[var(--bg-error)]',
            'text' => 'text-[var(--text-error)]',
            'icon' => 'fa-circle-exclamation',
        ],
    ];
@endphp

@foreach ((array) $messages as $message)
    <div class="alert-message {{ $config[$type]['bg'] }} {{ $config[$type]['text'] }} ps-2 pe-5 py-2 rounded-2xl flex items-center gap-2 fade-in leading-none tracking-wide">
        <i class='fas {{ $config[$type]['icon'] }} text-lg mr-1'></i>
        <p>{{ $message }}</p>
    </div>
@endforeach
