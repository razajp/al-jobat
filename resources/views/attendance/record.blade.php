@extends('app')
@section('title', 'Add Physical Quantities | ' . app('company')->name)
@section('content')
@php
    $category_options = [
        'a' => ['text'  => 'A'],
        'b' => ['text'  => 'B'],
];
@endphp
    <!-- Main Content -->
    <div class="max-w-2xl mx-auto">
        <x-search-header heading="Record Attendance" link linkText="Show Attendance" linkHref="{{ route('attendances.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('physical-quantities.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 max-w-2xl mx-auto relative overflow-hidden">
        @csrf
        <x-form-title-bar title="Record Attendance" />

        <div>
            <x-file-upload id="profile_picture" name="profile_picture" placeholder="{{ asset('images/xls_icon.png') }}"
                uploadText="Upload Profile Picture" class="h-50" imageSize="20" />
        </div>
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>

    <script>
        function validateForNextStep() {
            return true;
        }
    </script>
@endsection
