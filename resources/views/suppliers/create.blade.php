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
                    label="Name"
                    name="name" 
                    placeholder="Enter name" 
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
                <x-select 
                    label="Category" 
                    name="category_id" 
                    :options="$categories_options"
                    required
                    showDefault
                />
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
