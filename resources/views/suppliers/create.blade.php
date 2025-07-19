@extends('app')
@section('title', 'Add Suppliers | ' . app('company')->name)
@section('content')
    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Add Supplier" link linkText="Show Suppliers" linkHref="{{ route('suppliers.index') }}"/>
        <x-progress-bar 
            :steps="['Enter Details', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('suppliers.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-3xl mx-auto  relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Add Supplier" />
        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- supplier_name -->
                <x-input 
                    label="Supplier Name"
                    name="supplier_name" 
                    id="supplier_name" 
                    placeholder="Enter supplire name" 
                    required 
                    capitalized
                    dataValidate="required|friendly"
                />

                <!-- urdu_title -->
                <x-input 
                    label="Urdu Title"
                    name="urdu_title" 
                    id="urdu_title" 
                    placeholder="Enter urdu title" 
                    required 
                    dataValidate="required|urdu"
                />
                
                {{-- person name --}}
                <x-input 
                    label="Person Name"
                    name="person_name" 
                    id="person_name" 
                    placeholder="Enter person name" 
                    required 
                    capitalized
                    dataValidate="required|friendly"
                />

                {{-- supplier_phone_number --}}
                <x-input 
                    label="Phone Number" 
                    name="phone_number" 
                    id="phone_number" 
                    placeholder="Enter phone number" 
                    required
                    dataValidate="required|phone"
                />

                {{-- supplier_username --}}
                <x-input 
                    label="Username" 
                    name="username" 
                    id="username" 
                    type="username"
                    placeholder="Enter username" 
                    required
                    data-validate="required|alphanumeric|lowercase|unique:username"
                    data-clean="lowercase|alphanumeric|no-space"
                />

                {{-- supplier_password --}}
                <x-input 
                    label="Password" 
                    name="password" 
                    id="password" 
                    type="password" 
                    placeholder="Enter password" 
                    required 
                    dataValidate="required|min:4|alphanumeric|lowercase" 
                />

                {{-- supplier_registration_date --}}
                <x-input 
                    label="Date" 
                    name="date" 
                    id="date"
                    min="{{ now()->subMonth()->toDateString() }}"
                    validateMin
                    max="{{ now()->toDateString() }}"
                    validateMax
                    type="date"
                    required
                />

                {{-- supplier_category --}}
                <x-select 
                    label="Category"
                    id="category_select"
                    :options="$categories_options"
                    required
                    showDefault
                    onchange="trackStateOfCategoryBtn(this)"
                    class="grow"
                    withButton
                    btnId="addCategoryBtn"
                />

                <input type="hidden" name="categories_array" id="categories_array" value="">

                <hr class="col-span-2 border-gray-600">
                
                <div class="chipsContainer col-span-2">
                    <div id="chips" class="w-full flex gap-2">
                        <div class="chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 mx-auto fade-in">
                            <div class="text tracking-wide text-[var(--secondary-text)]">Please add category</div>
                        </div>
                    </div>
                    <div id="category-error" class="text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
                </div>
            </div>
        </div>

        <!-- Step 2: Production Details -->
        <div class="step2 hidden space-y-4">
            <x-image-upload 
                id="profile_picture"
                name="profile_picture"
                placeholder="{{ asset('images/image_icon.png') }}"
                uploadText="Upload Supplier's Picture"
            />
        </div>
    </form>

    <script>
        window.usernames = @json($usernames);

        function trackStateOfCategoryBtn(elem){
            if (elem.value != "") {
                addCategoryBtnDom.disabled = false;
            } else {
                addCategoryBtnDom.disabled = true;
            }
        }

        addCategoryBtnDom.addEventListener('click', () => {
            addCategory();
        })

        function addCategory() {
            if (categoriesArray.length <= 0) {
                chipsDom.innerHTML = '';
            }

            let selectedCategoryId = categorySelectDom.value;  // Get category ID
            
            let selectedCategoryName = categorySelectDom.parentElement.parentElement.parentElement.querySelector("ul li.selected").textContent.trim();  // Get category name

            // Check for duplicates based on ID
            if (categoriesArray.includes(selectedCategoryId)) {
                console.warn('Category already exists!');
                
                // Highlight the existing chip
                let existingChip = Array.from(chipsDom.children).find(chip => 
                    chip.getAttribute('data-id') === selectedCategoryId
                );

                if (existingChip) {
                    messageBox.innerHTML = `
                        <x-alert type="error" :messages="'This category already exists.'" />
                    `;
                    messageBoxAnimation();
                    existingChip.classList.add('bg-[var(--bg-error)]', 'transition', 'duration-300');
                    setTimeout(() => {
                        existingChip.classList.remove('bg-[var(--bg-error)]');
                    }, 5000);  // Remove highlight after 5 seconds
                    categorySelectDom.value = '';  // Clear selection
                    addCategoryBtnDom.disabled = true;  // Disable button
                    categorySelectDom.focus();
                }

                return;  // Stop the function if duplicate is found
            }

            if (selectedCategoryId) {
                // Create the chip element
                let chip = document.createElement('div');
                chip.className = 'chip border border-gray-600 text-[var(--secondary-text)] text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 fade-in';
                chip.setAttribute('data-id', selectedCategoryId);  // Store ID in a data attribute
                chip.innerHTML = `
                    <div class="text tracking-wide">${selectedCategoryName}</div>
                    <button class="delete cursor-pointer" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="size-3.5 stroke-[var(--secondary-text)]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;

                // Handle chip deletion
                chip.querySelector('.delete').onclick = () => {
                    chip.classList.add('fade-out');
                    
                    setTimeout(() => {
                        chip.remove();
                        categoriesArray = categoriesArray.filter(cat => cat !== selectedCategoryId);
                        
                        validateCategory()
                        
                        if (categoriesArray.length <= 0) {
                            chipsDom.innerHTML = `
                                <div class="chip border border-gray-600 text-[var(--secondary-text)] text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 mx-auto">
                                    <div class="text tracking-wide text-gray-400">Please add category</div>
                                </div>
                            `;
                        }

                        categoriesArrayInput.value = JSON.stringify(categoriesArray);  // Update hidden input with IDs
                    }, 300);
                }

                if (chipsDom) {
                    chipsDom.appendChild(chip);
                    categoriesArray.push(selectedCategoryId);  // Store category ID in array
                    categoriesArrayInput.value = JSON.stringify(categoriesArray);  // Update hidden input with IDs
                    categorySelectDom.value = '';  // Clear selection
                    addCategoryBtnDom.disabled = true;  // Disable button
                    categorySelectDom.focus();
                    validateCategory()
                } else {
                    console.error('Chip container not found!');
                }
            } else {
                console.warn('No category selected!');
            }
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
