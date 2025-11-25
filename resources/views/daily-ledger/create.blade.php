@extends('app')
@section('title', 'Daily Ledger Deposit | ' . app('company')->name)
@section('content')
@php
    $dailyLedgerType = Auth::user()->daily_ledger_type;

    $case_options = [
        'Add Copy A/c' => ['text' => 'Add Copy A/c'],
        'Big Box (Bara Dbba)' => ['text' => 'Big Box (Bara Dbba)'],
        'Zakat' => ['text' => 'Zakat'],
        'Sadqa' => ['text' => 'Sadqa'],
        'Charity' => ['text' => 'Charity'],
        'Daily Expenses' => ['text' => 'Daily Expenses'],
        'Exp 25# Plot' => ['text' => 'Exp 25# Plot'],
        'Home Expenses' => ['text' => 'Home Expenses'],
        'P.A Zubair bhai' => ['text' => 'P.A Zubair bhai'],
        'P.A Ali bhai' => ['text' => 'P.A Ali bhai'],
        'P.A Abdullah' => ['text' => 'P.A Abdullah'],
        'P.A Basit' => ['text' => 'P.A Basit'],
        'M.R Zubair Bhai' => ['text' => 'M.R Zubair Bhai'],
        'M.R Ali Bhai' => ['text' => 'M.R Ali Bhai'],
        'Return Amount' => ['text' => 'Return Amount'],
        'Staff Salary' => ['text' => 'Staff Salary'],
        'Utility Bill' => ['text' => 'Utility Bill'],
        'Weekly Payment' => ['text' => 'Weekly Payment'],
        'adjustment' => ['text' => 'Adjustment'],
    ];

    $method_options = [
        'cash' => ['text' => 'Cash'],
        'cheque' => ['text' => 'Cheque'],
        'less_copy_a_c' => ['text' => 'Less Copy A/c'],
        'online' => ['text' => 'Online'],
        'adjustment' => ['text' => 'Adjustment']
    ];
@endphp

    <div class="switch-btn-container flex absolute top-3 md:top-17 left-3 md:left-5 z-4">
        <div class="switch-btn relative flex border-3 border-[var(--secondary-bg-color)] bg-[var(--secondary-bg-color)] rounded-2xl overflow-hidden">
            <!-- Highlight rectangle -->
            <div id="highlight" class="absolute h-full rounded-xl bg-[var(--bg-color)] transition-all duration-300 ease-in-out z-0"></div>

            <!-- Buttons -->
            <button id="depositBtn" type="button" class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300" onclick="setVoucherType(this, 'deposit')">
                <div class="hidden md:block">Deposit</div>
                <div class="block md:hidden"><i class="fas fa-cart-shopping text-xs"></i></div>
            </button>
            <button id="useBtn" type="button" class="relative z-10 px-3.5 md:px-5 py-1.5 md:py-2 cursor-pointer rounded-xl transition-colors duration-300" onclick="setVoucherType(this, 'use')">
                <div class="hidden md:block">Use</div>
                <div class="block md:hidden"><i class="fas fa-box-open text-xs"></i></div>
            </button>
        </div>
    </div>

    <script>
        let btnTypeGlobal = "deposit";

        function setVoucherType(btn, btnType) {
            doHide = true;
            // check if its already selected
            if (btnTypeGlobal == btnType) {
                return;
            }

            $.ajax({
                url: "/set-daily-ledger-type",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    daily_ledger_type: btnType
                },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert("Failed to update daily ledger type.");
                    $(btn).prop("disabled", false);
                }
            });

            moveHighlight(btn, btnType);
        }

        function moveHighlight(btn, btnType) {
            const highlight = document.getElementById("highlight");
            const rect = btn.getBoundingClientRect();

            const parentRect = btn.parentElement.getBoundingClientRect();

            // Move and resize the highlight
            highlight.style.width = `${rect.width}px`;
            highlight.style.left = `${rect.left - parentRect.left - 3}px`;

            btnTypeGlobal = btnType;
        }

        // Initialize highlight on load
        window.onload = () => {
            @if($dailyLedgerType == 'deposit')
                const activeBtn = document.querySelector("#depositBtn");
                moveHighlight(activeBtn, "deposit");
            @else
                const activeBtn = document.querySelector("#useBtn");
                moveHighlight(activeBtn, "use");
            @endif
        };
    </script>

    <!-- Main Content -->
    <!-- header -->
    <div class="mb-5 max-w-3xl mx-auto">
        <x-search-header heading="{{ ucfirst($dailyLedgerType) }}" link linkText="Show Daily Ledger" linkHref="{{ route('daily-ledger.index') }}" />
    </div>

    <div class="row max-w-3xl mx-auto flex gap-4">
        <!-- Form -->
        <form id="form" action="{{ route('daily-ledger.store') }}" method="post" enctype="multipart/form-data"
            class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--glass-border-color)]/20 pt-14 grow relative overflow-hidden">
            @csrf
            <x-form-title-bar title="Daily Ledger {{ ucfirst($dailyLedgerType) }}" />
            <!-- Step: Basic Information -->
            <div class="step space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-full">
                        <!-- balance -->
                        <x-input label="Balance" value="{{ number_format($balance, 1) }}" disabled />
                    </div>

                    <!-- date -->
                    <x-input label="Date" name="date" id="date" type="date" validateMin min="2024-01-01" validateMax max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required />

                    @if ($dailyLedgerType === 'deposit')
                        {{-- method --}}
                        <x-select label="Method" name="method" id="method" :options="$method_options" required showDefault/>

                        <!-- reff_no -->
                        <x-input label="Reff. No." name="reff_no" id="reff_no" placeholder="Enter reff no" dataValidate="friendly" />
                    @else
                        {{-- case --}}
                        <x-select label="Case" name="case" id="case" :options="$case_options" required showDefault/>

                        <!-- remarks -->
                        <x-input label="Remarks" name="remarks" id="remarks" placeholder="Enter remarks" dataValidate="friendly" />
                    @endif

                    <!-- amount -->
                    <x-input label="Amount" id="amount" name="amount" type="amount" placeholder="Enter amount" required dataValidate="required|amount" />
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
