@extends('app')
@section('title', 'Add Setups | ' . app('company')->name)
@section('content')
<!-- Main Content -->
    <h1 class="text-3xl font-bold mb-5 text-center text-[--primary-color]">
        Add Setups
    </h1>

    <!-- Form -->
    <form id="add-setups-form" action="{{route('addSetup')}}" method="post"
        class="bg-[--secondary-bg-color] rounded-xl shadow-lg p-8 border border-[--h-bg-color] pt-12 max-w-lg mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[--primary-color] py-1 uppercase font-semibold text-sm">
            <h4>Add New Setups</h4>
        </div>
        <!-- Step 1: Basic Information -->
        <div id="step1" class="space-y-4 ">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <!-- title -->
                <x-input 
                    label="Title" 
                    name="title" 
                    id="title" 
                    type="text" 
                    placeholder="Enter Title" 
                    required 
                />

                <!-- title -->
                <x-input 
                    label="Short Title" 
                    name="short_title"  
                    id="short_title"  
                    type="text" 
                    placeholder="Enter Short Title"
                    uppercased
                />

                <!-- type -->
                <x-select 
                    label="Type" 
                    name="type" 
                    id="type" 
                    :options="[
                        'supplier_category' => ['text' => 'Supplier Category'],
                        'customer_category' => ['text' => 'Customer Category']
                    ]"
                    showDefault='true'
                />

                <!-- login Button -->
                <button type="submit"
                    class="w-full bg-[--primary-color] text-[--text-color] px-4 py-2 mt-2 rounded-lg hover:bg-[--h-primary-color] transition-all duration-300 ease-in-out font-medium uppercase">
                    Add
                </button>
            </div>
        </div>
    </form>
@endsection