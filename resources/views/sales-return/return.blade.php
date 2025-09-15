@php
    $authUser = Auth::user();
@endphp

@extends('app')
@section('title', 'Sales Return | ' . app('company')->name)
@section('content')
    <div class="mb-5 max-w-3xl mx-auto fade-in">
        <x-search-header heading="Sales Return" link linkText="Show Returns" linkHref="{{ route('sales-returns.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('users.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-3xl mx-auto relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Sales Return" />
        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-6 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Customer --}}
                <x-select label="Customer" name="customer" id="customer" :options="$customerOptions" showDefault onchange="onCustomerSelect(this)" />

                {{-- Article --}}
                <x-select label="Article" name="article" id="article" :options="[]" showDefault disabled />

                {{-- Invoice --}}
                <x-select label="Invoice" name="invoice" id="invoice" :options="[]" showDefault disabled />

                {{-- Quantity --}}
                <x-input label="Quantity" name="quantity" id="quantity" type="number" placeholder="Enter quantity" required disabled />
            </div>
        </div>
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>
    <script>
        function onCustomerSelect(selectElement) {
            const selectedCustomerId = selectElement.value;
            if (selectedCustomerId) {
                $.ajax({
                    url: "{{ route('sales-returns.get-details') }}",
                    type: 'POST',
                    data: {
                        customer_id: selectedCustomerId,
                        getArticles: true,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        const articleSelectDropdown = document.getElementById('article').parentElement.parentElement.parentElement.querySelector('.optionsDropdown');

                        let clutter = '<li data-for="article" data-value="" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)]" >-- Select Customer --</li>';
                        response.forEach(article => {
                            clutter += `<li data-for="article" data-value="${article.id}" onmousedown="selectThisOption(this)" class="py-2 px-3 cursor-pointer rounded-lg transition hover:bg-[var(--h-bg-color)] text-nowrap overflow-scroll my-scrollbar-2 hidden">${article.article_no}</li>`;
                        });
                        articleSelectDropdown.innerHTML = clutter;
                        document.getElementById('article').disabled = false;
                    },
                    error: function(xhr) {
                        console.error('Error fetching details:', xhr);
                    }
                });
            }
        }
    </script>
@endsection
