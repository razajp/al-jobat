@props([
    'type' => 'success',  {{-- success, warning, error --}}
    'messages' => [],
])

@php
    $config = [
        'success' => [
            'bg' => 'bg-[--bg-success]',
            'text' => 'text-[--text-success]',
            'border' => 'border-[--border-success]',
            'icon' => 'fa-circle-check',
        ],
        'warning' => [
            'bg' => 'bg-[--bg-warning]',
            'text' => 'text-[--text-warning]',
            'border' => 'border-[--border-warning]',
            'icon' => 'fa-triangle-exclamation',
        ],
        'error' => [
            'bg' => 'bg-[--bg-error]',
            'text' => 'text-[--text-error]',
            'border' => 'border-[--border-error]',
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
