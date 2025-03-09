@extends('app')
@section('title', 'Add Customer | ' . app('company')->name)
@section('content')
    <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color] fade-in"> Add Customer </h1>

    <!-- Progress Bar -->
    <div class="mb-5 max-w-2xl mx-auto">
        <x-progress-bar 
            :steps="['Enter Details', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('customers.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[--secondary-bg-color] text-sm rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-2xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add New Customer</h4>
        </div>
        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- customer_name -->
                <x-input 
                    label="Customer Name"
                    name="customer_name" 
                    id="customer_name" 
                    placeholder="Enter supplire name" 
                    required 
                />
                {{-- person name --}}
                <x-input 
                    label="Person Name"
                    name="person_name" 
                    id="person_name" 
                    placeholder="Enter person name" 
                    required 
                />

                {{-- customer_username --}}
                <x-input 
                    label="Username" 
                    name="username" 
                    id="username" 
                    placeholder="Enter username" 
                    class="lowercase placeholder:capitalize"
                    required
                />

                {{-- customer_password --}}
                <x-input 
                    label="Password" 
                    name="password" 
                    id="password" 
                    type="password" 
                    placeholder="Enter password" 
                    required 
                />

                {{-- customer_phone_number --}}
                <x-input 
                    label="Phone Number" 
                    name="phone_number" 
                    id="phone_number" 
                    type="text"
                    placeholder="Enter phone number"
                    required
                />

                {{-- customer_registration_date --}}
                <x-input 
                    label="Date" 
                    name="date" 
                    id="date" 
                    type="date"
                    required
                />

                {{-- customer_city --}}
                <x-input 
                    label="City" 
                    name="city" 
                    id="city"
                    placeholder="Enter city"
                    required
                />

                {{-- customer_category --}}
                <x-select 
                    label="Category"
                    name="category_id"
                    id="category_id"
                    :options="$categories_options"
                    required
                    showDefault
                />

                {{-- customer_address --}}
                <div class="col-span-2">
                    <x-input 
                        label="Address" 
                        name="address" 
                        id="address"
                        placeholder="Enter address"
                        required
                    />
                </div>
            </div>
        </div>

        <!-- Step 2: Production Details -->
        <div class="step2 hidden space-y-4">
            <x-image-upload 
                id="profile_picture"
                name="profile_picture"
                placeholder="{{ asset('images/image_icon.png') }}"
                uploadText="Upload Customer's Picture"
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

        const categorySelectDom = document.getElementById("category_select");
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
                        
                        validateCategory()
                        
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
                    validateCategory()
                } else {
                    console.error('Chip container not found!');
                }
            } else {
                console.warn('No category selected!');
            }
        }
        // Get DOM elements
        // const suppliers = ;
        // const supplierNameDom = document.getElementById('supplier_name');
        // const supplierNameError = document.getElementById('supplier_name-error');
        // const personNameDom = document.getElementById('person_name');
        // const personNameError = document.getElementById('person_name-error');
        // const usernameDom = document.getElementById('username');
        // const usernameError = document.getElementById('username-error');
        // const passwordDom = document.getElementById('password');
        // const passwordError = document.getElementById('password-error');
        // const phoneNumberDom = document.getElementById('phone_number');
        // const phoneNumberError = document.getElementById('phone_number-error');
        // const dateDom = document.getElementById('date');
        // const dateError = document.getElementById('date-error');
        // const categorySelectorDom = document.getElementById('category_select');
        // const categoryError = document.getElementById('category-error');
        // // const messageBox = document.getElementById("messageBox");

        // function validateSupplierName() {
        //     let supplierNameValue = supplierNameDom.value
        //     let isDuplicate = suppliers.some(s => s.supplier_name === supplierNameValue);

        //     if (!supplierNameValue) {
        //         supplierNameDom.classList.add("border-[--border-error]");
        //         supplierNameError.classList.remove("hidden");
        //         supplierNameError.textContent = "Supplier field is required.";
        //         return false;
        //     } else if (isDuplicate) {
        //         supplierNameDom.classList.add("border-[--border-error]");
        //         supplierNameError.classList.remove("hidden");
        //         supplierNameError.textContent = "This supplier already exists.";
        //         return false;
        //     } else {
        //         supplierNameDom.classList.remove("border-[--border-error]");
        //         supplierNameError.classList.add("hidden");
        //         return true;
        //     }
        // }
        
        // function validatePersonName() {
        //     let personNameValue = personNameDom.value
        //     if (personNameValue == "") {
        //         personNameDom.classList.add("border-[--border-error]");
        //         personNameError.classList.remove("hidden");
        //         personNameError.textContent = "Person name field is required.";
        //         return false;
        //     } else {
        //         personNameDom.classList.remove("border-[--border-error]");
        //         personNameError.classList.add("hidden");
        //         return true;
        //     }
        // }
        
        // function validateUsername() {
        //     let usernameValue = usernameDom.value.trim(); // Remove leading and trailing spaces
        //     let isDuplicate = suppliers.some(s => s.user.username === usernameValue);
        //     let hasSpaces = /\s/.test(usernameValue); // Check for spaces using regex
            
        //     if (hasSpaces) {
        //         usernameDom.classList.add("border-[--border-error]");
        //         usernameError.classList.remove("hidden");
        //         usernameError.textContent = "Username should not contain spaces.";
        //         return false;
        //     } else if (!usernameValue) {
        //         usernameDom.classList.add("border-[--border-error]");
        //         usernameError.classList.remove("hidden");
        //         usernameError.textContent = "Username field is required.";
        //         return false;
        //     } else if (isDuplicate) {
        //         usernameDom.classList.add("border-[--border-error]");
        //         usernameError.classList.remove("hidden");
        //         usernameError.textContent = "This username already exists.";
        //         return false;
        //     } else {
        //         usernameDom.classList.remove("border-[--border-error]");
        //         usernameError.classList.add("hidden");
        //         return true;
        //     }
        // }
        
        // function validatePassword() {
        //     let PasswordValue = passwordDom.value
        //     if (PasswordValue == "") {
        //         passwordDom.classList.add("border-[--border-error]");
        //         passwordError.classList.remove("hidden");
        //         passwordError.textContent = "Password field is required.";
        //         return false;
        //     } else if (PasswordValue.length < 4) {
        //         passwordDom.classList.add("border-[--border-error]");
        //         passwordError.classList.remove("hidden");
        //         passwordError.textContent = "Password must be at least 4 characters.";
        //         return false;
        //     } else {
        //         passwordDom.classList.remove("border-[--border-error]");
        //         passwordError.classList.add("hidden");
        //         return true;
        //     }
        // }
        
        // function validatePhoneNumber() {
        //     let phoneNo = phoneNumberDom.value.replace(/\D/g, '').trim();
        //     let isDuplicate = suppliers.some(s => s.phone_number.replace(/\D/g, '') === phoneNo);
            
        //     if (!phoneNo) {
        //         phoneNumberDom.classList.add("border-[--border-error]");
        //         phoneNumberError.classList.remove("hidden");
        //         phoneNumberError.textContent = "Phone number field is required.";
        //         return false;
        //     } else if (isDuplicate) {
        //         phoneNumberDom.classList.add("border-[--border-error]");
        //         phoneNumberError.classList.remove("hidden");
        //         phoneNumberError.textContent = "This phone number already exists.";
        //         return false;
        //     } else {
        //         phoneNumberDom.classList.remove("border-[--border-error]");
        //         phoneNumberError.classList.add("hidden");
        //         return true;
        //     }
        // }
        
        // function validateDate() {
        //     let dateValue = dateDom.value;
            
        //     if (!dateValue) {
        //         dateDom.classList.add("border-[--border-error]");
        //         dateError.classList.remove("hidden");
        //         dateError.textContent = "Date field is required.";
        //         return false;
        //     } else {
        //         dateDom.classList.remove("border-[--border-error]");
        //         dateError.classList.add("hidden");
        //         return true;
        //     }
        // }
        
        // function validateCategory() {
        //     const categoriesLength = categoriesArray.length;
            
        //     if (categorySelectorDom.value == '' && categoriesLength <= 0) {
        //         categorySelectorDom.classList.add("border-[--border-error]");
        //         categoryError.classList.remove("hidden");
        //         categoryError.textContent = "Please select or add a category.";
        //         return false;
        //     } else if (categoriesLength <= 0) {
        //         categorySelectorDom.classList.add("border-[--border-error]");
        //         categoryError.classList.remove("hidden");
        //         categoryError.textContent = "Please add a category.";
        //         return false;
        //     } else {
        //         categorySelectorDom.classList.remove("border-[--border-error]");
        //         categoryError.classList.add("hidden");
        //         return true;
        //     }
        // }

        // // 🔹 **Live Validation Events**
        // supplierNameDom.addEventListener("input", validateSupplierName);
        // personNameDom.addEventListener("input", validatePersonName);
        // usernameDom.addEventListener("input", validateUsername);
        // passwordDom.addEventListener("input", validatePassword);
        // phoneNumberDom.addEventListener("input", validatePhoneNumber);
        // dateDom.addEventListener("change", validateDate);
        // categorySelectorDom.addEventListener("change", validateCategory);

        // function validateForNextStep() {
        //     let isValidSupplierName = validateSupplierName();
        //     let isValidPersonName = validatePersonName();
        //     let isValidUsername = validateUsername();
        //     let isValidPassword = validatePassword();
        //     let isValidPhoneNumber = validatePhoneNumber();
        //     let isValidDate = validateDate();
        //     let isValidCategory = validateCategory();

        //     let isValid = isValidSupplierName && isValidPersonName && isValidUsername && isValidPassword && isValidPhoneNumber && isValidDate && isValidCategory;

        //     if (!isValid) {
        //         messageBox.innerHTML = `
        //             {{-- <x-alert type="error" :messages="'Invalid details, please correct them.'" /> --}}
        //         `;
        //         messageBoxAnimation();
        //     } else {
        //         isValid = true
        //     }

        //     return isValid;
        // }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
