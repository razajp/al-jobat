@extends('app')
@section('title', 'Add Customer | ' . app('company')->name)
@section('content')
    <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color] fade-in"> Add Supplier </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-2xl mx-auto">
        <x-progress-bar 
            :steps="['Enter Details', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('suppliers.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-2xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add New Supplier</h4>
        </div>
        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- supplier_name -->
                <x-input 
                    label="Supplier Name"
                    name="supplier_name" 
                    placeholder="Enter supplire name" 
                    required 
                />
                {{-- person name --}}
                <x-input 
                    label="Person Name"
                    name="person_name" 
                    placeholder="Enter person name" 
                    required 
                />

                {{-- supplier_username --}}
                <x-input 
                    label="Username" 
                    name="username" 
                    placeholder="Enter username" 
                    required 
                />

                {{-- supplier_password --}}
                <x-input 
                    label="Password" 
                    name="password" 
                    type="password" 
                    placeholder="Enter password" 
                    required 
                />

                {{-- supplier_phone_number --}}
                <x-input 
                    label="Phone Number" 
                    name="phone_number" 
                    type="text"
                    placeholder="Enter phone number" 
                    required
                />

                {{-- supplier_registration_date --}}
                <x-input 
                    label="Date" 
                    name="date" 
                    type="date"
                    required
                />

                {{-- supplier_category --}}
                <div class="col-span-2">
                    <x-select 
                        label="Category"
                        id="category_id"
                        :options="$categories_options"
                        required
                        showDefault
                        class="grow"
                        withButton
                        btnId="addCategoryBtn"
                    />
                </div>

                <input type="hidden" name="categories_array" id="categories_array" value="">

                <hr class="col-span-2 border-gray-600">
                
                <div class="chipsContainer col-span-2">
                    <div id="chips" class="w-full flex gap-2">
                        {{-- <div class="chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2">
                            <div class="text tracking-wide">Fabric</div>
                            <button class="delete" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                class="size-3 stroke-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div> --}}
                        
                        <div class="chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 mx-auto fade-in">
                            <div class="text tracking-wide text-gray-400">Please add category</div>
                        </div>
                    </div>
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
        function formatPhoneNo(input) {
            let value = input.value.replace(/\D/g, ''); // Remove all non-numeric characters

            if (value.length > 4) {
                value = value.slice(0, 4) + '-' + value.slice(4, 11); // Insert hyphen after 4 digits
            }

            input.value = value; // Update the input field
        }

        document.getElementById('phone_number').addEventListener('input', function() {
            formatPhoneNo(this);
        });

        const categorySelectDom = document.getElementById("category_id");
        const addCategoryBtnDom = document.getElementById("addCategoryBtn");
        const chipsDom = document.getElementById("chips");
        const categoriesArrayInput = document.getElementById("categories_array");
        let categoriesArray = [];
        addCategoryBtnDom.disabled = true;

        categorySelectDom.addEventListener("change", (e) => {
            trackStateOfCategoryBtn(e.target.value);
        })

        function trackStateOfCategoryBtn(value){
            if (value != "") {
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
            let selectedCategoryName = categorySelectDom.options[categorySelectDom.selectedIndex].text;  // Get category name

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
                    existingChip.classList.add('bg-[--bg-error]', 'transition', 'duration-300');
                    setTimeout(() => {
                        existingChip.classList.remove('bg-[--bg-error]');
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
                chip.className = 'chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 fade-in';
                chip.setAttribute('data-id', selectedCategoryId);  // Store ID in a data attribute
                chip.innerHTML = `
                    <div class="text tracking-wide">${selectedCategoryName}</div>
                    <button class="delete" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="size-3 stroke-gray-300">
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
                        
                        if (categoriesArray.length <= 0) {
                            chipsDom.innerHTML = `
                                <div class="chip border border-gray-600 text-gray-300 text-xs rounded-xl py-2 px-4 inline-flex items-center gap-2 mx-auto">
                                    <div class="text tracking-wide text-gray-400">Please add category</div>
                                </div>
                            `;
                        }

                        categoriesArrayInput.value = JSON.stringify(categoriesArray);  // Update hidden input with IDs
                    }, 300);
                };

                if (chipsDom) {
                    chipsDom.appendChild(chip);
                    categoriesArray.push(selectedCategoryId);  // Store category ID in array
                    categoriesArrayInput.value = JSON.stringify(categoriesArray);  // Update hidden input with IDs
                    categorySelectDom.value = '';  // Clear selection
                    addCategoryBtnDom.disabled = true;  // Disable button
                    categorySelectDom.focus();
                } else {
                    console.error('Chip container not found!');
                }
            } else {
                console.warn('No category selected!');
            }
        }
        // Get DOM elements
        // const customer = document.getElementById('customer');
        // const customers = {{-- @json($customers) --}};
        // const customerError = document.getElementById('customer-error');
        // const person_name = document.getElementById('person_name');
        // const person_nameError = document.getElementById('person_name-error');
        // const phone = document.getElementById('phone');
        // const phoneError = document.getElementById('phone-error');
        // const city = document.getElementById('city');
        // const cityError = document.getElementById('city-error');
        // const address = document.getElementById('address');
        // const addressError = document.getElementById('address-error');
        // // const messageBox = document.getElementById("messageBox");

        // function showError(input, errorElement, message) {
        //     input.classList.add("border-[--border-error]");
        //     errorElement.classList.remove("hidden");
        //     errorElement.textContent = message;
        //     return false; // Return false on error
        // }

        // function hideError(input, errorElement) {
        //     input.classList.remove("border-[--border-error]");
        //     errorElement.classList.add("hidden");
        //     return true; // Return true if no error
        // }

        // function validateCustomerName() {
        //     let customerName = customer.value.trim().toLowerCase();
        //     let cityName = city.value.trim().toLowerCase();
        //     let existingCustomer = customers.find(c => 
        //         c.customer.toLowerCase() === customerName && c.city.toLowerCase() === cityName
        //     );

        //     if (!customerName || customerName === "m/s") {
        //         return showError(customer, customerError, "Customer name is required.");
        //     } else if (existingCustomer) {
        //         return showError(customer, customerError, `This customer already exists in ${existingCustomer.city}.`);
        //     } else {
        //         return hideError(customer, customerError);
        //     }
        // }

        // function validatePersonName() {
        //     let personName = person_name.value.trim().toLowerCase();
        //     return personName && personName !== "mr."
        //         ? hideError(person_name, person_nameError)
        //         : showError(person_name, person_nameError, "Person name is required.");
        // }

        // function validatePhoneNumber() {
        //     let phoneNo = phone.value.replace(/\D/g, '').trim();
        //     let isDuplicate = customers.some(c => c.phone.replace(/\D/g, '') === phoneNo);

        //     if (!phoneNo) {
        //         return showError(phone, phoneError, "Phone number is required.");
        //     } else if (isDuplicate) {
        //         return showError(phone, phoneError, "This phone number is already registered.");
        //     } else {
        //         return hideError(phone, phoneError);
        //     }
        // }

        // function validateCity() {
        //     let cityName = city.value.trim();
        //     return cityName ? hideError(city, cityError) : showError(city, cityError, "City is required.");
        // }

        // function validateAddress() {
        //     let addressText = address.value.trim();
        //     return addressText ? hideError(address, addressError) : showError(address, addressError, "Address is required.");
        // }

        // // 🔹 **Live Validation Events**
        // customer.addEventListener("input", validateCustomerName);
        // city.addEventListener("input", validateCustomerName);
        // person_name.addEventListener("input", validatePersonName);
        // phone.addEventListener("input", validatePhoneNumber);
        // city.addEventListener("input", validateCity);
        // address.addEventListener("input", validateAddress);

        // function validateForNextStep(){
        //     let isValid = validateCustomerName() 
        //         || validatePersonName() 
        //         || validatePhoneNumber() 
        //         || validateCity() 
        //         || validateAddress();

        //     if (!isValid) {
        //         messageBox.innerHTML = `
        //             <div id="warning-message"
        //                 class="bg-[--bg-warning] text-[--text-warning] border border-[--border-warning] px-5 py-2 rounded-2xl flex items-center gap-2 fade-in">
        //                 <i class='bx bxs-error-alt'></i>
        //                 <p>Invalid details, please correct them.</p>
        //             </div>
        //         `;
        //         messageBoxAnimation();
        //     }

        //     return isValid;
        // }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
