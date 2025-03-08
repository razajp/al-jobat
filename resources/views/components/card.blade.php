{{-- Status Dot and Label --}}
@if (isset($data['status']))
    <div
        class="active_inactive_dot absolute top-2 right-2 w-[0.6rem] h-[0.6rem] rounded-full {{ $data['status'] === 'active' ? 'bg-[--border-success]' : ($data['status'] === 'transparent' ? 'bg-transparent' : ($data['status'] === 'no_Image' ? 'bg-[--border-warning]' : 'bg-[--border-error]')) }}">
    </div>
    <div
        class="active_inactive absolute text-xs {{ $data['status'] === 'active' ? 'text-[--border-success]' : ($data['status'] === 'transparent' ? 'text-transparent' : ($data['status'] === 'no_Image' ? 'text-[--border-warning]' : 'text-[--border-error]')) }} top-1 right-2 h-[1rem]">
        {{ ucfirst(str_replace('_', ' ', $data['status'])) }}
    </div>
@endif

{{-- Profile Picture --}}
<div class="{{ $data['classImg'] ?? '' }} img aspect-square h-full rounded-full overflow-hidden relative">
    <img src="{{ $data['image'] }}" alt="" class="w-full h-full object-cover">
</div>

{{-- Details --}}
<div class="text-start">
    <h5 class="text-xl my-1 text-[--text-color] capitalize font-semibold">
        {{ $data['name'] ?? 'N/A' }}
    </h5>
    @if (isset($data['details']) && is_array($data['details']))
        @foreach ($data['details'] as $label => $value)
            <p class="text-[--secondary-text] tracking-wide text-sm capitalize">
                <strong>{{ $label }}:</strong> <span>{{ $value }}</span>
            </p>
        @endforeach
    @endif
</div>

{{-- Action Button --}}
<button
    class="absolute bottom-0 right-0 rounded-full w-[22%] aspect-square flex items-center justify-center bg-[--h-bg-color] text-lg translate-x-1/4 translate-y-1/4 transition-all duration-200 ease-in-out hover:scale-110">
    <i class='fas fa-arrow-right text-2xl -rotate-45'></i>
</button>