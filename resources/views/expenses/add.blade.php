@extends('app')
@section('title', 'Add Article | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- Progress Bar -->
    <div class="mb-5 max-w-5xl mx-auto">
        <x-search-header heading="Add Expense" link linkText="Show Expenses" linkHref="{{ route('expenses.index') }}"/>
        <x-progress-bar 
            :steps="['Enter Details', 'Enter Rates', 'Upload Image']" 
            :currentStep="1"
        />
    </div>

    <div class="row max-w-5xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('expenses.store') }}" method="post" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 grow relative overflow-hidden">
            @csrf
            <x-form-title-bar title="Add Expense" />
            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- date -->
                    <x-input 
                        label="Date"
                        name="date" 
                        id="date"
                        validateMin
                        min="{{ now()->subDays('14')->toDateString() }}"
                        validateMax
                        max="{{ now()->toDateString() }}"
                        type="date" 
                        required 
                    />
                    
                    {{-- supplier --}}
                    <x-select 
                        label="Supplier"
                        name="supplier"
                        id="supplier"
                        :options="$suppliers_options"
                        required
                        showDefault
                        onchange="getSupplierData(this)"
                    />

                    <!-- balance -->
                    <x-input 
                        label="Balance"
                        id="balance" 
                        type="number"
                        disabled
                        placeholder="Balance"
                    />
                    
                    {{-- expense --}}
                    <x-select 
                        label="Expense"
                        name="expense"
                        id="expense"
                        required
                        showDefault
                    />

                    <!-- expense_no -->
                    <x-input 
                        label="Reff. No."
                        name="reff_no" 
                        id="reff_no" 
                        type="number" 
                        placeholder="Enter reff no" 
                        required
                    />

                    <!-- amount -->
                    <x-input 
                        label="Amount"
                        name="amount" 
                        id="amount" 
                        type="number"
                        placeholder="Enter amount " 
                        required
                    />

                    <!-- lot_no -->
                    <x-input 
                        label="Lot No."
                        name="lot_no" 
                        id="lot_no" 
                        type="number"
                        placeholder="Enter lot no" 
                        required
                    />

                    {{-- remarks --}}
                    <x-input 
                        label="Remarks" 
                        name="remarks" 
                        id="remarks" 
                        type="text"
                        placeholder="Enter remarks" 
                    />
                </div>
            </div>

            <!-- Step 2: Production Details -->
            <div class="step2 hidden space-y-4">
                <div class="step2 hidden space-y-4 ">
                    <div class="flex justify-between gap-4">
                        {{-- title --}}
                        <div class="grow">
                            <x-input 
                                id="title" 
                                placeholder="Enter title" 
                            />
                        </div>
                        
                        {{-- rate --}}
                        <x-input 
                            id="rate" 
                            type="number"
                            placeholder="Enter rate" 
                        />

                        {{-- add rate button --}}
                        <div class="form-group flex w-10 shrink-0">
                            <input type="button" value="+"
                                class="w-full bg-[var(--primary-color)] text-[var(--text-color)] rounded-lg cursor-pointer border border-[var(--primary-color)]"
                                onclick="addRate()" />
                        </div>
                    </div>
                    {{-- rate showing --}}
                    <div id="rate-table" class="w-full text-left text-sm">
                        <div class="flex justify-between items-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4 mb-4">
                            <div class="grow ml-5">Title</div>
                            <div class="w-1/4">Rate</div>
                            <div class="w-[10%] text-center">Action</div>
                        </div>
                        <div id="rate-list" class="space-y-4 h-[250px] overflow-y-auto my-scrollbar-2">
                            <div class="text-center bg-[var(--h-bg-color)] rounded-lg py-2 px-4">No Rates Added</div>
                        </div>
                    </div>
                    {{-- calc bottom --}}
                    <div id="calc-bottom" class="flex w-full gap-4 text-sm">
                        <div
                            class="total flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4 w-full cursor-not-allowed">
                            <div>Total - Rs.</div>
                            <div class="text-right">0.00</div>
                        </div>
                        <div
                            class="final flex justify-between items-center bg-[var(--h-bg-color)] border border-gray-600 rounded-lg py-2 px-4 w-full">
                            <label for="sales_rate" class="text-nowrap grow">Sales Rate - Rs.</label>
                            <input type="text" required name="sales_rate" id="sales_rate" value="0.00"
                                class="text-right bg-transparent outline-none border-none w-[50%]" />
                        </div>
                    </div>
                    <input type="hidden" name="rates_array" id="rates_array" value="[]" />
                </div>
            </div>

            <!-- Step 3: Image -->
            <div class="step3 hidden space-y-4">
                <x-image-upload 
                    id="image_upload"
                    name="image_upload"
                    placeholder="{{ asset('images/image_icon.png') }}"
                    uploadText="Upload article image"
                />
            </div>
        </form>

        <div
            class="bg-[var(--secondary-bg-color)] rounded-xl shadow-xl p-8 border border-[var(--h-bg-color)] w-[35%] pt-12 relative overflow-hidden fade-in">
            <x-form-title-bar title="Last Record" />

            <!-- Step 1: Basic Information -->
            <div class="step1 space-y-4 ">
                
            </div>

            <!-- Step 2: Production Details -->
            <div class="step2 hidden space-y-6  h-full flex flex-col">
                
            </div>

            <!-- Step 3: Production Details -->
            <div class="step3 hidden space-y-6  text-sm">
                
            </div>
        </div>
    </div>

    <script>
        function getSupplierData(supplierElem) {
            console.log(supplierElem);
        }
    </script>
@endsection