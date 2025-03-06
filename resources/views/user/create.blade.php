@php
    $authUser = Auth::user();

    $roleOptions = [
        'guest' => 'Guest',
        'accountant' => 'Accountant'
    ];

    if ($authUser->role == 'developer') {
        $roleOptions['admin'] = 'Admin';
        $roleOptions['owner'] = 'Owner';
    }

    if ($authUser->role == 'owner') {
        $roleOptions['admin'] = 'Admin';
    }
@endphp

@extends('app')
@section('title', 'Add User | Al Jobat')
@section('content')
    <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color]">
        Add User
    </h1>

    
    <div class="mb-5 max-w-2xl mx-auto fade-in">
        <x-progress-bar 
            :steps="['Enter Details', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('users.store') }}" method="post" enctype="multipart/form-data"
        class="bg-[--secondary-bg-color] rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-2xl mx-auto relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 uppercase font-semibold text-sm">
            <h4>Add New User</h4>
        </div>
        <!-- Step 1: Basic Information -->
        <div class="step1 space-y-6 ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <x-input 
                label="Name" 
                name="name" 
                placeholder="Enter name" 
                required 
                />

                {{-- Username --}}
                <x-input 
                label="Username" 
                name="username" 
                placeholder="Enter username" 
                required 
                />

                {{-- Password --}}
                <x-input 
                label="Password" 
                name="password" 
                type="password" 
                placeholder="Enter password" 
                required 
                />

                {{-- Role --}}
                <x-select 
                label="Role" 
                name="role" 
                :options="$roleOptions"
                />
            </div>
        </div>

        <!-- Step 2: Production Details -->
        <div class="step2 hidden space-y-6 ">
            <x-image-upload 
                id="profile_picture"
                name="profile_picture"
                placeholder="{{ asset('images/image_icon.png') }}"
                uploadText="Upload Profile Picture"
            />
        </div>
    </form>
    <script>
        const nameDom = document.getElementById("name");
        const nameError = document.getElementById("name-error");
        const users = @json($users);
        const usernameDom = document.getElementById("username");
        const usernameError = document.getElementById("username-error");
        const passwordDom = document.getElementById("password");
        const passwordError = document.getElementById("password-error");

        function validateName() {
            // Validate Name
            if (nameDom.value.trim() === "") {
                nameDom.classList.add("border-[--border-error]");
                nameError.classList.remove("hidden");
                nameError.textContent = "Name field is required.";
                return false;
            } else {
                nameDom.classList.remove("border-[--border-error]");
                nameError.classList.add("hidden");
                return true;
            }
        }

        function validateUsername() {
           // Validate Username
           if (usernameDom.value.trim() === "") {
                usernameDom.classList.add("border-[--border-error]");
                usernameError.classList.remove("hidden");
                usernameError.textContent = "Username field is required.";
                return false;
            } else if (users.some(user => user.username === usernameDom.value.trim())) {
                usernameDom.classList.add("border-[--border-error]");
                usernameError.classList.remove("hidden");
                usernameError.textContent = "Username is already taken.";
                return false;
            } else {
                usernameDom.classList.remove("border-[--border-error]");
                usernameError.classList.add("hidden");
                return true;
            }
        }

        function validatePassword() {
            // Validate Password
            if (passwordDom.value.trim() === "") {
                passwordDom.classList.add("border-[--border-error]");
                passwordError.classList.remove("hidden");
                passwordError.textContent = "Password field is required.";
                return false;
            } else if (passwordDom.value.length < 3) {
                passwordDom.classList.add("border-[--border-error]");
                passwordError.classList.remove("hidden");
                passwordError.textContent = "Password must be at least 3 characters.";
                return false;
            } else {
                passwordDom.classList.remove("border-[--border-error]");
                passwordError.classList.add("hidden");
                return true;
            }
        }
        
        passwordDom.addEventListener("input", function () {
            validatePassword()
        })
        
        usernameDom.addEventListener("input", function () {
            validateUsername()
        });

        nameDom.addEventListener("input", function(){
            validateName()
        });

        function validateForNextStep() {
            let isValidName = validateName();
            let isValidUsername = validateUsername();
            let isValidPasswird = validatePassword();

            let isValid = isValidName || isValidUsername || isValidPasswird;

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
