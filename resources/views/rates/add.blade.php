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
                    onchange="trackTypeStatus(this)"
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
                    onchange="trackEffectiveDateState(this)"
                    disabled
                />
            </div>
        </div>

        <!-- Step 2: Basic Information -->
        <div class="step2 space-y-4 hidden">
            <div class="inputsWrapper grid grid-cols-1 md:grid-cols-1 gap-4">
            </div>
        </div>
    </form>
    <script>
        const articleDetails = @json(app('article'));
        function trackTypeStatus(elem) {
            if (elem.value != '') {
                document.querySelector('#effective_date').disabled = false;

                let step2 = document.querySelector('.step2 .inputsWrapper');

                if (elem.value == 'cutting') {  
                    step2.innerHTML = `
                        <!-- select_category -->
                        <x-input 
                            label="Selet Category" 
                            name="select_category" 
                            id="select_category" 
                            required
                            placeholder="Select Category"
                            readonly
                            onclick="generateSelectCategoryModal()"
                        />
                    `;
                }
            } else {
                document.querySelector('#effective_date').disabled = true;
            }
        }

        function trackEffectiveDateState(elem) {
            gotoStep(2);
        }

        function generateSelectCategoryModal() {
            let categoriesArray = Object.entries(articleDetails.categories);
            let cardData = [];

            if (categoriesArray.length > 0) {
                categoriesArray.forEach(([key, value]) => {
                    cardData.push({
                        id: key,
                        name: value.text,
                        checkbox: true,
                    })
                });
            }

            let categoryModalData = {
                id: "categoryModalForm",
                class: 'h-[60%] w-full',
                cards: {name: 'Select Category', count: 4, data: cardData},
            }
            
            createModal(categoryModalData);
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection