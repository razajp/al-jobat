<!-- Modal Wrapper Component -->
@props(['id', 'closeAction', 'action' => '#', 'method' => 'POST', 'classForBody' => ''])

<form id="{{ $id }}" method="{{ $method }}" action="{{ $action }}" enctype="multipart/form-data" class="w-full h-full flex flex-col space-y-4 relative items-center justify-center">
    @csrf
    <!-- Modal Box -->
    <div class="{{ $classForBody }} bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg w-full max-w-2xl p-6 flex relative">
        <div id="modal-close"
            class="absolute top-0 -right-4 translate-x-full bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg w-auto p-3 text-sm">
            <!-- Close Button -->
            <button onclick="{{ $closeAction }}()" type="button"
                class="z-10 text-gray-400 hover:text-gray-600 transition-all 0.3s ease-in-out cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                    class="w-6 h-6" style="display: inline">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Slot for Dynamic Content -->
        <div class="flex flex-col w-full">
            <div class="w-full h-full">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Modal Actions -->
    <div id="modal-action"
        class="bg-[var(--secondary-bg-color)] rounded-2xl shadow-lg max-w-3xl w-auto p-3 relative text-sm">
        <div class="flex gap-4">
            {{ $actions ?? '' }}
        </div>
    </div>
</form>
