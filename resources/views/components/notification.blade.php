@props([
    'title' => '',
    'message' => '',
    'timestamp' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])
<div class="notification-card bg-[var(--glass-border-color)]/5 backdrop-blur-md text-[var(--secondary-text)] px-5 py-4 border border-[var(--glass-border-color)]/20 rounded-2xl shadow-xl flex items-start gap-4 fade-in relative">
    <div class="flex-1">
        @if($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <p class="text-sm">{{ $message }}</p>

        @if($actionLabel && $actionUrl)
            <a href="{{ $actionUrl }}" class="text-xs font-medium mt-2 inline-block bg-[var(--glass-border-color)]/5 backdrop-blur-md me-1 px-4 py-2 border border-[var(--glass-border-color)]/20 rounded-lg transition-all duration-300 ease-in-out hover:opacity-80">
                {{ $actionLabel }}
            </a>
        @endif
    </div>

    <button class="absolute top-2.5 right-3.5 text-md opacity-60 hover:opacity-100 transition-all duration-300 ease-in-out cursor-pointer" onclick="this.parentElement.remove()">
        <i class="fas fa-xmark"></i>
    </button>
</div>