@extends('app')
@section('title', 'Add Bilty | ' . app('company')->name)
@section('content')
    <!-- Main Content -->

    <div class="max-w-4xl mx-auto">
        <x-search-header heading="Add Bilty" link linkText="Show Bilties" linkHref="{{ route('bilties.index') }}"/>
    </div>

    <!-- Form -->
    <form id="form" action="{{ route('bilties.store') }}" method="post"
        class="bg-[var(--secondary-bg-color)] text-sm rounded-xl shadow-lg p-8 border border-[var(--h-bg-color)] pt-12 max-w-4xl mx-auto  relative overflow-hidden">
        @csrf
        <div
            class="form-title text-center absolute top-0 left-0 w-full bg-[var(--primary-color)] py-1 capitalize tracking-wide font-medium text-sm">
            <h4>Add Physical Quantity</h4>
        </div>

        <div class="space-y-4 ">
            <div class="flex justify-between gap-4">
                {{-- article --}}
                <div class="grow">
                    <x-input label="Article" id="article" placeholder='Select Article' class="cursor-pointer" withImg imgUrl="" readonly required />
                    <input type="hidden" name="article_id" id="article_id" value="" />
                </div>

                {{-- date --}}
                <div class="w-1/3">
                    <x-input label="Date" name="date" id="date" type="date" required />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-4">
                {{-- pcs_per_packet  --}}
                <div>
                    <x-input label="Pcs./packet" name="pcs_per_packet" id="pcs_per_packet" type="number" placeholder="Enter pcs. count per packet" required />
                </div>

                {{-- packets --}}
                <div>
                    <x-input label="Packets" name="packets" id="packets" type="number" placeholder="Enter packet count" required />
                </div>
            </div>

            <hr class="border-gray-600 my-3">

            <div class="flex w-full gap-4 text-sm mt-5 items-start">
                <div class="first w-full">
                    <div class="current-phys-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Total Physical Stock - Pcs</div>
                        <div id="currentPhysicalQuantity">0</div>
                    </div>
                </div>
                <div class="second w-full">
                    <div class="total-qty flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Total Quantity - Pcs</div>
                        <div id="finalOrderedQuantity">0</div>
                    </div>
                    <div id="total-qty-error" class="text-[var(--border-error)] text-xs mt-1 hidden transition-all duration-300 ease-in-out"></div>
                </div>
                <div class="thered w-full">
                    <div class="final flex justify-between items-center border border-gray-600 rounded-lg py-2 px-4">
                        <div class="grow">Total Amount - Rs.</div>
                        <div id="finalOrderAmount">0.0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full flex justify-end mt-4">
            <button type="submit"
                class="px-6 py-1 bg-[var(--bg-success)] border border-[var(--bg-success)] text-[var(--text-success)] font-medium text-nowrap rounded-lg hover:bg-[var(--h-bg-success)] transition-all duration-300 ease-in-out cursor-pointer">
                <i class='fas fa-save mr-1'></i> Save
            </button>
        </div>
    </form>
@endsection
