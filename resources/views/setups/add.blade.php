@extends('app')
@section('title', 'Add Setups | ' . app('company')->name)
@section('content')
<!-- Main Content -->

    <div class="max-w-lg mx-auto">
        <x-search-header heading="Add Setup" link linkText="Show Setups" linkHref="{{ route('setups.index') }}"/>
    </div>

    <!-- Form -->
    <form id="add-setups-form" action="{{route('setups.store')}}" method="post"
        class="bg-[var(--secondary-bg-color)] rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-lg mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Setups" />

        <!-- Step 1: Basic Information -->
        <div id="step1" class="space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <!-- type -->
                <x-select
                    label="Type"
                    name="type"
                    id="type"
                    :options="[
                        'supplier_category' => ['text' => 'Supplier Category'],
                        'bank_name' => ['text' => 'Bank Name'],
                        'city' => ['text' => 'City'],
                        'fabric' => ['text' => 'Fabric'],
                        'staff_type' => ['text' => 'Staff Type'],
                        'utility_bill_type' => ['text' => 'Utility Bill Type'],
                        'utility_bill_location' => ['text' => 'Utility Bill Location'],
                    ]"
                    showDefault
                />

                <!-- title -->
                <x-input
                    label="Title"
                    name="title"
                    id="title"
                    type="text"
                    placeholder="Enter Title"
                    required
                    capitalized
                />

                <!-- title -->
                <x-input
                    label="Short Title"
                    name="short_title"
                    id="short_title"
                    type="text"
                    placeholder="Enter Short Title"
                    uppercased
                />

                <!-- login Button -->
                <button type="submit"
                    class="w-full bg-[var(--primary-color)] text-[var(--text-color)] px-4 py-2 mt-2 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out font-medium uppercase cursor-pointer">
                    Add
                </button>
            </div>
        </div>
    </form>
@endsection
