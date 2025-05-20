@extends('app')
@section('title', 'Edit Customer | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Edit Customer" link linkText="Show Customers" linkHref="{{ route('customers.index') }}"/>
        <x-progress-bar 
            :steps="['Enter Details', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <div class="row max-w-3xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('customers.update', ['customer' => $customer->id]) }}" method="POST" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 grow relative overflow-hidden">
            @csrf
            @method('PUT')
            <div
                class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
                <h4>Edit Article</h4>
            </div>
            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- customer_name -->
                    <x-input 
                        label="Customer Name"
                        value="{{ $customer->customer_name }}"
                        disabled
                    />

                    {{-- person name --}}
                    <x-input 
                        label="Person Name"
                        value="{{ $customer->person_name }}"
                        disabled
                    />

                    {{-- customer_phone_number --}}
                    <x-input 
                        label="Phone Number" 
                        name="phone_number" 
                        id="phone_number" 
                        value="{{ $customer->phone_number }}"
                        placeholder="Enter phone number"
                        required
                    />

                    {{-- customer_address --}}
                    <x-input 
                        label="Address" 
                        name="address" 
                        id="address"
                        value="{{ $customer->address }}"
                        placeholder="Enter address"
                        required
                    />
                </div>
            </div>

            <!-- Step 2: Image -->
            <div class="step2 hidden space-y-4">
                @if ($customer->user->profile_picture == 'default_avatar.png')
                    <x-image-upload 
                        id="image_upload"
                        name="image_upload"
                        placeholder="{{ asset('images/image_icon.png') }}"
                        uploadText="Upload customer image"
                    />
                @else
                    <x-image-upload 
                        id="image_upload"
                        name="image_upload"
                        placeholder="{{ asset('storage/uploads/images/' . $customer->user->profile_picture) }}"
                        uploadText="Preview"
                    />
                    <script>
                        const placeholderIcon = document.querySelector(".placeholder_icon");
                        placeholderIcon.classList.remove("w-16", "h-16");
                        placeholderIcon.classList.add("rounded-md", "w-full", "h-auto");
                    </script>
                @endif
            </div>
        </form>
    </div>

    <script>
        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
