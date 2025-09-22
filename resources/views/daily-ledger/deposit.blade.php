@extends('app')
@section('title', 'Daily Ledger Deposit | ' . app('company')->name)
@section('content')
    <!-- Main Content -->
    <!-- header -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="Deposit" link linkText="Show Daily Ledger" linkHref="{{ route('daily-ledger.index') }}" />
    </div>

    <div class="row max-w-3xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('daily-ledger.deposit-store') }}" method="post" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 grow relative overflow-hidden">
            @csrf
            <x-form-title-bar title="Daily Ledger Deposit" />
            <!-- Step: Basic Information -->
            <div class="step space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-full">
                        <!-- balance -->
                        <x-input label="Balance" value="{{ number_format($balance, 1) }}" disabled />
                    </div>

                    <!-- date -->
                    <x-input label="Date" name="date" id="date" type="date" validateMin min="{{ now()->subDay(1)->toDateString() }}" validateMax max="{{ now()->toDateString() }}" value="{{ now()->subDay(1)->toDateString() }}" required />

                    {{-- method --}}
                    <x-select label="Method" name="method" id="method" :options="[
                        'cash' => ['text' => 'Cash'],
                        'online' => ['text' => 'Online']
                    ]" required showDefault/>

                    <!-- amount -->
                    <x-input label="Amount" id="amount" name="amount" type="amount" placeholder="Enter amount" required dataValidate="required|amount" />

                    <!-- reff_no -->
                    <x-input label="Reff. No." name="reff_no" id="reff_no" placeholder="Enter reff no" dataValidate="required|friendly" />
                </div>
            </div>

            <div class="w-full flex justify-end mt-4">
                <button type="submit"
                    class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all 0.3s ease-in-out cursor-pointer">
                    <i class='fas fa-save mr-1'></i> Save
                </button>
            </div>
        </form>
    </div>
@endsection
