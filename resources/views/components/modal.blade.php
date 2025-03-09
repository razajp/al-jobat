<!-- Modal Wrapper Component -->
@props(['id', 'closeAction', 'action' => '#', 'method' => 'POST', 'classForBody' => ''])

<form id="{{ $id }}" method="{{ $method }}" action="{{ $action }}" enctype="multipart/form-data" class="w-full h-full flex flex-col space-y-4 items-center justify-center">
    @csrf

    <!-- Modal Box -->
    <div class="{{ $classForBody }} bg-[--secondary-bg-color] rounded-xl shadow-lg w-full max-w-2xl p-6 relative flex">
        <!-- Close Button -->
        <button onclick="{{ $closeAction }}()" type="button"
            class="absolute top-3 right-3 z-10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all 0.3s ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Slot for Dynamic Content -->
        <div class="flex flex-col w-full">
            <div class="w-full h-full">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Modal Actions -->
    <div id="modal-action"
        class="bg-[--secondary-bg-color] rounded-2xl shadow-lg max-w-3xl w-auto p-3 relative text-sm">
        <div class="flex gap-4">
            {{ $actions ?? '' }}
        </div>
    </div>
</form>
