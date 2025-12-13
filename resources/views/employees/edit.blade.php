@extends('app')
@section('title', 'Edit Employee | ' . app('client_company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Edit Employee" link linkText="Show Employee" linkHref="{{ route('employees.index') }}"/>
        <x-progress-bar
            :steps="['Enter Details', 'Upload Image']"
            :currentStep="1"
        />
    </div>

    <div class="row max-w-3xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('employees.update', ['employee' => $employee->id]) }}" method="POST" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 grow relative overflow-hidden">
            @csrf
            @method('PUT')
            <x-form-title-bar title="Edit Employee" />

            <!-- Step1 : Basic Information -->
            <div class="step1 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- employee_category --}}
                    <x-input
                        label="Category"
                        value="{{ Str::ucfirst($employee->category) }}"
                        disabled
                    />

                    {{-- employee_type --}}
                    <x-select
                        label="Type"
                        name="type_id"
                        id="type"
                        :options="$types_options"
                        required
                    />

                    <!-- employee_name -->
                    <x-input
                        label="Employee Name"
                        value="{{ $employee->employee_name }}"
                        disabled
                    />

                    <!-- urdu_title -->
                    <x-input
                        label="Urdu Title"
                        name="urdu_title"
                        value="{{ $employee->urdu_title }}"
                        placeholder="Enter urdu title"
                    />

                    {{-- employee_phone_number --}}
                    <x-input
                        label="Phone Number"
                        name="phone_number"
                        value="{{ $employee->phone_number }}"
                        placeholder="Enter phone number"
                        required
                        oninput="formatPhoneNo(this)"
                    />

                    {{-- employee_joining_date --}}
                    <x-input
                        label="Joining Date"
                        value="{{ $employee->joining_date->format('d-M-Y, D') }}"
                        disabled
                    />

                    {{-- employee_cnic --}}
                    <x-input
                        label="C.N.I.C No."
                        name="cnic_no"
                        value="{{ $employee->cnic_no }}"
                        placeholder="Enter C.N.I.C No."
                        capitalized
                        oninput="formatCnicNo(this)"
                    />

                    {{-- employee_salary --}}
                    <x-input
                        label="Salary"
                        name="salary"
                        value="{{ $employee->salary }}"
                        placeholder="Enter salary"
                        type="number"
                        :disabled="$employee->category !== 'staff'"
                        :required="$employee->category !== 'staff'"
                        capitalized
                    />
                </div>
            </div>

            <!-- Step 2: Image -->
            <div class="step2 hidden space-y-4">
                @if ($employee->profile_picture == 'default_avatar.png')
                    <x-file-upload
                        id="image_upload"
                        name="image_upload"
                        placeholder="{{ asset('images/image_icon.png') }}"
                        uploadText="Upload employee image"
                    />
                @else
                    <x-file-upload
                        id="image_upload"
                        name="image_upload"
                        placeholder="{{ asset('storage/uploads/images/' . $employee->profile_picture) }}"
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
        let employee = @json($employee);

        document.addEventListener('DOMContentLoaded', function () {
            const option = document.querySelector('li[data-value="{{ $employee->type_id }}"]');
            if (option) {
                selectThisOption(option);
            }
        });

        function formatPhoneNo(input) {
            let value = input.value.replace(/\D/g, ''); // Remove all non-numeric characters

            if (value.length > 4) {
                value = value.slice(0, 4) + '-' + value.slice(4, 11); // Insert hyphen after 4 digits
            }

            input.value = value; // Update the input field
        }

        function formatCnicNo(input) {
            let value = input.value.replace(/\D/g, ''); // Remove all non-numeric characters

            if (value.length > 5 && value.length <= 12) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            if (value.length > 12) {
                value = value.slice(0, 5) + '-' + value.slice(5, 12) + '-' + value.slice(12, 13);
            }

            input.value = value; // Update the input field
        }

        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
