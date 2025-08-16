@extends('app')
@section('title', 'Generate Order | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-4xl mx-auto">
        <x-search-header heading="Statement"/>
        <x-progress-bar :steps="['Generate Statement', 'Preview']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Generate Statement" />

        <!-- Step 1: Generate Staement -->
        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- category --}}
                <x-select
                    label="Category"
                    name="category"
                    id="category"
                    :options="[
                        'supplier' => ['text' => 'Supplier'],
                        'customer' => ['text' => 'Customer'],
                    ]"
                    showDefault
                    onchange="fetchNames(this.value)"
                />

                {{-- name --}}
                <x-select
                    label="Name"
                    name="name"
                    id="nameSelect"
                    :options="[]"
                    showDefault
                />

                <!-- date_from -->
                <x-input
                    label="Date From"
                    name="date_from"
                    id="date_from"
                    validateMin
                    min="{{ now()->subDays('14')->toDateString() }}"
                    validateMax
                    max="{{ now()->toDateString() }}"
                    type="date"
                    required
                />

                <!-- date_to -->
                <x-input
                    label="Date To"
                    name="date_to"
                    id="date_to"
                    validateMin
                    min="{{ now()->subDays('14')->toDateString() }}"
                    validateMax
                    max="{{ now()->toDateString() }}"
                    type="date"
                    required
                />
            </div>
        </div>

        <!-- Step 2: view order -->
        <div class="step2 hidden space-y-4 text-black h-[35rem] overflow-y-auto my-scrollbar-2 bg-white rounded-md">
            <div id="preview-container" class="w-[210mm] h-[297mm] mx-auto overflow-hidden relative">
                <div id="preview" class="preview flex flex-col h-full">
                    <h1 class="text-[var(--border-error)] font-medium text-center mt-5">No Preview avalaible.</h1>
                </div>
            </div>
        </div>
    </form>

    <script>
        const nameSelectDropDown = document.getElementById('nameSelect').parentElement.parentElement.parentElement.querySelector('ul.optionsDropdown');

        function fetchNames(category) {
            if (!category) {
                return;
            }

            nameSelectDropDown.innerHTML = '<option value="">Select Name</option>'; // Reset options

            $.ajax({
                url: "{{ route('reports.statement.get-names') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    category: category,
                },
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(item) {
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.name;
                            nameSelectDropDown.appendChild(option);
                        });

                        nameSelectDropDown.disabled = false; // Enable the select if names are found
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No names found';
                        nameSelectDropDown.appendChild(option);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching names:', error);
                }
            });
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
