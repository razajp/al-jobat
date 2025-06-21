{{-- Status Dot and Label --}}
@if (isset($data['status']))
    <div
        class="active_inactive_dot absolute top-2 right-2 w-[0.6rem] h-[0.6rem] rounded-full {{ $data['status'] === 'active' ? 'bg-[var(--border-success)]' : ($data['status'] === 'transparent' ? 'bg-transparent' : ($data['status'] === 'no_Image' ? 'bg-[var(--border-warning)]' : 'bg-[var(--border-error)]')) }}">
    </div>
    <div
        class="active_inactive absolute top-2 right-2 text-xs capitalize {{ $data['status'] === 'active' ? 'text-[var(--border-success)]' : ($data['status'] === 'transparent' ? 'text-transparent' : ($data['status'] === 'no_Image' ? 'text-[var(--border-warning)]' : 'text-[var(--border-error)]')) }} h-[1rem]">
        {{ ucfirst(str_replace('_', ' ', $data['status'])) }}
    </div>
@endif

{{-- Profile Picture --}}
@if (isset($data['image']))
    <div class="{{ $data['classImg'] ?? '' }} img aspect-square h-full rounded-[41.5%] overflow-hidden relative">
        <img src="{{ $data['image'] }}" loading="lazy" alt="" class="w-full h-full object-cover">
    </div>
@endif

{{-- Details --}}
<div class="text-start {{ isset($data['image']) ? "pt-1" : "" }}">
    <h5 class="text-xl mb-2 text-[var(--text-color)] capitalize font-semibold leading-none">
        {{ $data['name'] ?? 'N/A' }}
    </h5>
    @if (isset($data['details']) && is_array($data['details']))
        @foreach ($data['details'] as $label => $value)
            <p class="text-[var(--secondary-text)] tracking-wide text-sm capitalize">
                <strong>{{ $label }}:</strong> <span style="opacity: 0.9">{{ $value }}</span>
            </p>
        @endforeach
    @endif
</div>

{{-- Action Button --}}
<button type="button"
    class="absolute bottom-0 right-0 rounded-full w-[25%] aspect-square flex items-center justify-center text-lg translate-x-1/4 translate-y-1/4 transition-all duration-200 ease-in-out cursor-pointer">
    <div class="absolute top-0 left-0 bg-[var(--h-bg-color)] blur-md rounded-full h-50 aspect-square"></div>
    <i class='fas fa-arrow-right text-2xl -rotate-45'></i>
</button>