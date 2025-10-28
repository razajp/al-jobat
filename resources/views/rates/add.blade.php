@extends('app')
@section('title', 'Add Rates | ' . app('company')->name)
@section('content')
    <!-- Main Content -->

    <div class="max-w-2xl mx-auto">
        <x-search-header heading="Add Setup" link linkText="Show Rates" linkHref="{{ route('rates.index') }}" />
        <x-progress-bar :steps="['Select Type', 'Enter Rates']" :currentStep="1" />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('rates.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-2xl mx-auto relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Rates" />

        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- type -->
                <x-select label="Type" name="type_id" id="type" :options="$type_options" showDefault
                    onchange="trackTypeStatus(this)" />

                <!-- effective_date -->
                <x-input label="Effective Date" name="effective_date" id="effective_date" type="date" validateMin
                    min="2024-01-01" required onchange="trackEffectiveDateState(this)" disabled />
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
        // let type = '';

        function trackTypeStatus(elem) {
            if (elem.value != '') {
                document.querySelector('#effective_date').disabled = false;

                let step2 = document.querySelector('.step2 .inputsWrapper');

                if (elem.closest('.selectParent').querySelector('ul[data-for="type"] li.selected').textContent.trim() == 'Cutting') {
                    step2.innerHTML = `
                        <!-- select_categories -->
                        <x-input
                            label="Selet Categories"
                            id="select_categories"
                            required
                            placeholder="Select Categories"
                            readonly
                            onclick="generateSelectCredentialsModal('categories')"
                        />

                        <!-- select_seasons -->
                        <x-input
                            label="Selet Seasons"
                            id="select_seasons"
                            required
                            placeholder="Select Seasons"
                            readonly
                            onclick="generateSelectCredentialsModal('seasons')"
                        />

                        <!-- select_sizes -->
                        <x-input
                            label="Selet Sizes"
                            id="select_sizes"
                            required
                            placeholder="Select Sizes"
                            readonly
                            onclick="generateSelectCredentialsModal('sizes')"
                        />

                        <input type="hidden" name="categories" />
                        <input type="hidden" name="seasons" />
                        <input type="hidden" name="sizes" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- title -->
                            <x-input label="Title" name="title" id="title"
                                placeholder="Enter title" required />

                            <!-- rate -->
                            <x-input label="Rate" name="rate" id="rate" type="number"
                                placeholder="Enter rate" required />
                        </div>
                    `;
                }
            } else {
                document.querySelector('#effective_date').disabled = true;
            }
        }

        function trackEffectiveDateState(elem) {
            gotoStep(2);
        }

        function generateSelectCredentialsModal(type) {
            // let type = typeText;
            let typesArray = Object.entries(articleDetails[type]);
            let cardData = [];

            if (typesArray.length > 0) {
                typesArray.forEach(([key, value]) => {
                    cardData.push({
                        id: key,
                        name: value.text,
                        checkbox: true,
                        checked: value.selected || false,
                        data: {
                            key,
                            value
                        },
                        onclick: `selectThisCard(this, "${type}")`,
                    })
                });
            }

            let credentialsModalData = {
                id: "credentialsModalForm",
                class: 'h-[60%] w-full',
                cards: {
                    name: `Select ${type}`,
                    count: 4,
                    data: cardData
                },
            }

            createModal(credentialsModalData);
        }

        function selectThisCard(elem, type) {
            let selecttypeInp = document.getElementById(`select_${type}`);
            let selecttypeInpDB = document.querySelector(`input[name="${type}"]`);
            let selectedtype = JSON.parse(elem.dataset.json);
            let selectedtypeId = selectedtype.id;
            let selectedtypeInDetails = articleDetails[type][selectedtypeId];
            let checkbox = elem.querySelector("input[type='checkbox']")
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                selectedtypeInDetails.selected = true;
            } else {
                selectedtypeInDetails.selected = false;
            }

            // let selectedTypes = Object.values(articleDetails[type]).filter(t => t.selected === true);
            let selectedTypes = Object.entries(articleDetails[type]).filter(([key, value]) => value.selected === true);

            let selectedTexts = selectedTypes.map(([key, value]) => value.text).join(' | ');

            selecttypeInp.value = selectedTexts;
            selecttypeInpDB.value = JSON.stringify(selectedTypes.map(([key, value]) => key)) || '';
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
