<!-- resources/views/components/modal.blade.php -->
@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'action' => '#',
    'method' => 'POST',
    'image' => null,
    'content' => '',
    'buttons' => [],
    'closeAction' => 'closeModal()',
])

<!-- Modal Container -->
<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 text-sm fade-in">
    <!-- Modal Content -->
    <form method="{{ $method }}" action="{{ $action }}" class="bg-[--secondary-bg-color] rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        @csrf

        <!-- Close Button -->
        <button onclick="{{ $closeAction }}" type="button"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all 0.3s ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Modal Body -->
        <div class="modal_body flex items-start">
            @if($image)
                <div class="w-1/5 h-1/5">
                    <img src="{{ asset($image) }}" alt=""
                        class="w-full h-full object-cover">
                </div>
            @endif
            <div class="content ml-5">
                <h2 class="text-xl font-semibold text-[--text-color]">{{ $title }}</h2>
                <div class="text-sm text-[--secondary-text] mt-2 mb-6">
                    {!! $content !!}
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3">
            @foreach ($buttons as $button)
                <button type="{{ $button['type'] ?? 'button' }}"
                    onclick="{{ $button['onclick'] ?? '' }}"
                    class="px-4 py-2 {{ $button['class'] }} transition-all 0.3s ease-in-out">
                    {{ $button['label'] }}
                </button>
            @endforeach
        </div>
    </form>
</div>