@extends('app')
@section('title', 'Issue Fabric | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Issue Fabric" link linkText="Show Fabrics" linkHref="{{ route('fabrics.index') }}" />
    </div>

    <div class="row max-w-3xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('fabrics.issuePost') }}" method="post" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 grow relative overflow-hidden">
            @csrf
            <x-form-title-bar title="Issue Fabric" />
            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- date -->
                    <x-input label="Date" name="date" id="date" validateMin min="2024-01-01" validateMax max="{{ now()->toDateString() }}" type="date" required />

                    {{-- tag --}}
                    <x-select label="Tag" name="tag" id="tag" :options="$tags_options" required showDefault onchange="trackTagSelect(this)" />

                    {{-- worker --}}
                    <x-select label="Worker" name="worker_id" id="worker" :options="$workers_options" required showDefault />

                    <!-- unit -->
                    <x-input label="Unit" name="unit" id="unit" placeholder="Unit" capitalized disabled />

                    <!-- avalaible_stock -->
                    <x-input label="Avalaible Stock" name="avalaible_stock" id="avalaible_stock" type="number" placeholder="Avalaible Stock" disabled />

                    <!-- quantity -->
                    <x-input label="Quantity" name="quantity" id="quantity" type="number" placeholder="Enter quantity" required oninput="trackQuantity(this)" />

                    <div class="col-span-full">
                        {{-- remarks --}}
                        <x-input label="Remarks" name="remarks" id="remarks" type="text" placeholder="Enter remarks" />
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-end mt-4">
                <button type="submit"
                    class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                    <i class='fas fa-save mr-1'></i> Save
                </button>
            </div>
        </form>
    </div>

    <script>
        const unitInoDom = document.getElementById('unit');
        const avalaibleStockInpDom = document.getElementById('avalaible_stock');
        const quantityErrorDom = document.getElementById('quantity-error');

        function trackTagSelect(elem) {
            const selectedTag = JSON.parse(elem.parentElement.parentElement.parentElement.querySelector('li.selected').getAttribute('data-option') ?? '{}');
            unitInoDom.value = selectedTag.unit;
            avalaibleStockInpDom.value = selectedTag.avalaible_sock;
        }

        function trackQuantity(elem) {
            const maxStock = parseInt(avalaibleStockInpDom.value || '0');
            const currentVal = parseInt(elem.value || '0');

            if (currentVal > maxStock) {
                elem.value = maxStock;
                quantityErrorDom.textContent = 'Maximum';
                quantityErrorDom.classList.remove('hidden');
            } else {
                elem.value = currentVal;
            }
        }
    </script>
@endsection
