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
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-3xl mx-auto  relative overflow-hidden">
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
                />

                <!-- urdu_title -->
                <x-input 
                    label="Urdu Title"
                    name="urdu_title" 
                    id="urdu_title" 
                    placeholder="Enter urdu title" 
                    required 
                />
                
                {{-- person name --}}
                <x-input 
                    label="Person Name"
                    name="person_name" 
                    id="person_name" 
                    placeholder="Enter person name" 
                    required 
                    capitalized
                />

                {{-- supplier_phone_number --}}
                <x-input 
                    label="Phone Number" 
                    name="phone_number" 
                    id="phone_number" 
                    placeholder="Enter phone number" 
                    required
                />

                {{-- supplier_username --}}
                <x-input 
                    label="Username" 
                    name="username" 
                    id="username" 
                    type="username"
                    placeholder="Enter username" 
                    required
                />

                {{-- supplier_password --}}
                <x-input 
                    label="Password" 
                    name="password" 
                    id="password" 
                    type="password" 
                    placeholder="Enter password" 
                    required 
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
        // Get DOM elements
        const suppliers = @json($suppliers);
        const usernames = @json($usernames);
        const supplierNameDom = document.getElementById('supplier_name');
        const supplierNameError = document.getElementById('supplier_name-error');
        const urduTitleDom = document.getElementById('urdu_title');
        const urduTitleError = document.getElementById('urdu_title-error');
        const personNameDom = document.getElementById('person_name');
        const personNameError = document.getElementById('person_name-error');
        const usernameDom = document.getElementById('username');
        const usernameError = document.getElementById('username-error');
        const passwordDom = document.getElementById('password');
        const passwordError = document.getElementById('password-error');
        const phoneNumberDom = document.getElementById('phone_number');
        const phoneNumberError = document.getElementById('phone_number-error');
        const dateDom = document.getElementById('date');
        const dateError = document.getElementById('date-error');
        const categorySelectorDom = document.getElementById('category_select');
        const categoryError = document.getElementById('category-error');
        // const messageBox = document.getElementById("messageBox");

        function validateSupplierName() {
            let supplierNameValue = supplierNameDom.value
            let isDuplicate = suppliers.some(s => s.supplier_name === supplierNameValue);

            if (!supplierNameValue) {
                supplierNameDom.classList.remove("border-gray-600");
                supplierNameDom.classList.add("border-[var(--border-error)]");
                supplierNameError.classList.remove("hidden");
                supplierNameError.textContent = "Supplier field is required.";
                return false;
            } else if (isDuplicate) {
                supplierNameDom.classList.remove("border-gray-600");
                supplierNameDom.classList.add("border-[var(--border-error)]");
                supplierNameError.classList.remove("hidden");
                supplierNameError.textContent = "This supplier already exists.";
                return false;
            } else {
                supplierNameDom.classList.add("border-gray-600");
                supplierNameDom.classList.remove("border-[var(--border-error)]");
                supplierNameError.classList.add("hidden");
                return true;
            }
        }

        function validateUrduTitle() {
            let urduTitleDomValue = urduTitleDom.value

            if (urduTitleDomValue == "") {
                urduTitleDom.classList.remove("border-gray-600");
                urduTitleDom.classList.add("border-[var(--border-error)]");
                urduTitleError.classList.remove("hidden");
                urduTitleError.textContent = "Urdu title field is required.";
                return false;
            } else {
                urduTitleDom.classList.add("border-gray-600");
                urduTitleDom.classList.remove("border-[var(--border-error)]");
                urduTitleError.classList.add("hidden");
                return true;
            }
        }
        
        function validatePersonName() {
            let personNameValue = personNameDom.value
            if (personNameValue == "") {
                personNameDom.classList.remove("border-gray-600");
                personNameDom.classList.add("border-[var(--border-error)]");
                personNameError.classList.remove("hidden");
                personNameError.textContent = "Person name field is required.";
                return false;
            } else {
                personNameDom.classList.add("border-gray-600");
                personNameDom.classList.remove("border-[var(--border-error)]");
                personNameError.classList.add("hidden");
                return true;
            }
        }
        
        function validateUsername() {
            let usernameValue = usernameDom.value.trim(); // Remove leading and trailing spaces
            let isDuplicate = usernames.some(u => u === usernameValue);
            let hasSpaces = /\s/.test(usernameValue); // Check for spaces using regex
            
            if (hasSpaces) {
                usernameDom.classList.remove("border-gray-600");
                usernameDom.classList.add("border-[var(--border-error)]");
                usernameError.classList.remove("hidden");
                usernameError.textContent = "Username should not contain spaces.";
                return false;
            } else if (!usernameValue) {
                usernameDom.classList.remove("border-gray-600");
                usernameDom.classList.add("border-[var(--border-error)]");
                usernameError.classList.remove("hidden");
                usernameError.textContent = "Username field is required.";
                return false;
            } else if (usernameDom.value.length < 6) {
                usernameDom.classList.remove("border-gray-600");
                usernameDom.classList.add("border-[var(--border-error)]");
                usernameError.classList.remove("hidden");
                usernameError.textContent = "Username must be at least 6 characters.";
                return false;
            } else if (isDuplicate) {
                usernameDom.classList.remove("border-gray-600");
                usernameDom.classList.add("border-[var(--border-error)]");
                usernameError.classList.remove("hidden");
                usernameError.textContent = "This username already exists.";
                return false;
            } else {
                usernameDom.classList.add("border-gray-600");
                usernameDom.classList.remove("border-[var(--border-error)]");
                usernameError.classList.add("hidden");
                return true;
            }
        }
        
        function validatePassword() {
            let PasswordValue = passwordDom.value
            if (PasswordValue == "") {
                passwordDom.classList.remove("border-gray-600");
                passwordDom.classList.add("border-[var(--border-error)]");
                passwordError.classList.remove("hidden");
                passwordError.textContent = "Password field is required.";
                return false;
            } else if (PasswordValue.length < 4) {
                passwordDom.classList.remove("border-gray-600");
                passwordDom.classList.add("border-[var(--border-error)]");
                passwordError.classList.remove("hidden");
                passwordError.textContent = "Password must be at least 4 characters.";
                return false;
            } else {
                passwordDom.classList.add("border-gray-600");
                passwordDom.classList.remove("border-[var(--border-error)]");
                passwordError.classList.add("hidden");
                return true;
            }
        }
        
        function validatePhoneNumber() {
            let phoneNo = phoneNumberDom.value.replace(/\D/g, '').trim();
            let isDuplicate = suppliers.some(s => s.phone_number.replace(/\D/g, '') === phoneNo);
            
            if (!phoneNo) {
                phoneNumberDom.classList.remove("border-gray-600");
                phoneNumberDom.classList.add("border-[var(--border-error)]");
                phoneNumberError.classList.remove("hidden");
                phoneNumberError.textContent = "Phone number field is required.";
                return false;
            } else if (isDuplicate) {
                phoneNumberDom.classList.remove("border-gray-600");
                phoneNumberDom.classList.add("border-[var(--border-error)]");
                phoneNumberError.classList.remove("hidden");
                phoneNumberError.textContent = "This phone number already exists.";
                return false;
            } else {
                phoneNumberDom.classList.add("border-gray-600");
                phoneNumberDom.classList.remove("border-[var(--border-error)]");
                phoneNumberError.classList.add("hidden");
                return true;
            }
        }
        
        function validateDate() {
            let dateValue = dateDom.value;
            
            if (!dateValue) {
                dateDom.classList.remove("border-gray-600");
                dateDom.classList.add("border-[var(--border-error)]");
                dateError.classList.remove("hidden");
                dateError.textContent = "Date field is required.";
                return false;
            } else {
                dateDom.classList.add("border-gray-600");
                dateDom.classList.remove("border-[var(--border-error)]");
                dateError.classList.add("hidden");
                return true;
            }
        }
        
        function validateCategory() {
            const categoriesLength = categoriesArray.length;
            
            if (categorySelectorDom.value == '' && categoriesLength <= 0) {
                categorySelectorDom.classList.remove("border-gray-600");
                categorySelectorDom.classList.add("border-[var(--border-error)]");
                categoryError.classList.remove("hidden");
                categoryError.textContent = "Please select or add a category.";
                return false;
            } else if (categoriesLength <= 0) {
                categorySelectorDom.classList.remove("border-gray-600");
                categorySelectorDom.classList.add("border-[var(--border-error)]");
                categoryError.classList.remove("hidden");
                categoryError.textContent = "Please add a category.";
                return false;
            } else {
                categorySelectorDom.classList.add("border-gray-600");
                categorySelectorDom.classList.remove("border-[var(--border-error)]");
                categoryError.classList.add("hidden");
                return true;
            }
        }

        // 🔹 **Live Validation Events**
        supplierNameDom.addEventListener("input", validateSupplierName);
        urduTitleDom.addEventListener("input", validateUrduTitle);
        personNameDom.addEventListener("input", validatePersonName);
        usernameDom.addEventListener("input", validateUsername);
        passwordDom.addEventListener("input", validatePassword);
        phoneNumberDom.addEventListener("input", validatePhoneNumber);
        dateDom.addEventListener("change", validateDate);
        categorySelectorDom.addEventListener("change", validateCategory);

        function validateForNextStep() {
            let isValidSupplierName = validateSupplierName();
            let isValidUrduTitle = validateUrduTitle();
            let isValidPersonName = validatePersonName();
            let isValidUsername = validateUsername();
            let isValidPassword = validatePassword();
            let isValidPhoneNumber = validatePhoneNumber();
            let isValidDate = validateDate();
            let isValidCategory = validateCategory();

            let isValid = isValidSupplierName && isValidUrduTitle && isValidPersonName && isValidUsername && isValidPassword && isValidPhoneNumber && isValidDate && isValidCategory;

            if (!isValid) {
                messageBox.innerHTML = `
                    <x-alert type="error" :messages="'Invalid details, please correct them.'" />
                `;
                messageBoxAnimation();
            } else {
                isValid = true
            }

            return isValid;
        }
    </script>
@endsection
