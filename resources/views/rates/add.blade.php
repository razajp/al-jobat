@extends('app')
@section('title', 'Add Rates | ' . app('company')->name)
@section('content')
<!-- Main Content -->

    <div class="max-w-2xl mx-auto">
        <x-search-header heading="Add Setup" link linkText="Show Rates" linkHref="{{ route('rates.index') }}"/>
        <x-progress-bar :steps="['Select Type', 'Enter Rates']" :currentStep="1"/>
    </div>

    <!-- Form -->
    <form id="add-rates-form" action="{{route('rates.store')}}" method="post"
        class="bg-[var(--secondary-bg-color)] rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-2xl mx-auto relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Rates" />

        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- type -->
                <x-select 
                    label="Type" 
                    name="type" 
                    id="type" 
                    :options="[
                        'cutting' => ['text' => 'Cutting'],
                    ]"
                    showDefault
                />

                <!-- effective_date -->
                <x-input 
                    label="Effective Date" 
                    name="effective_date" 
                    id="effective_date" 
                    type="date" 
                    validateMin
                    min="{{ now()->toDateString() }}"
                    required
                />
            </div>
        </div>

        <!-- Step 2: Basic Information -->
        <div class="step2 space-y-4 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- effective_date -->
                <x-input 
                    label="Effective Date" 
                    name="effective_date" 
                    id="effective_date" 
                    type="date" 
                    validateMinh
                    min="{{ now()->toDateString() }}"
                    required
                />
            </div>
        </div>
    </form>
    <script>
        function validateForNextStep() {
            return true;
        }
    </script>
@endsection