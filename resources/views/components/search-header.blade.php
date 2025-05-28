@props([
    'heading' => '',
    'filter_items' => [],
    'toFrom' => false,
    'toFrom_type' => 'text',
    'toFrom_label' => 'text',
    'link' => false,
    'linkText' => '',
    'linkHref' => '#',
])

<div class="header w-full flex items-center justify-between">
    <h5 id="name" class="text-3xl text-[var(--text-color)] uppercase font-semibold leading-none ml-1">{{ $heading }}</h5>

    <div class="">
        @if ($toFrom)
            <!-- toFrom -->
            <div id="toFrom" class="toFrom-box flex items-center gap-4 shrink-0 grow w-full">
                <label for="to" class="block font-medium text-[var(--secondary-text)] grow text-nowrap">{{ $toFrom_label }}</label>
                <div class="toFrom-inputs relative grid grid-cols-2 gap-4 w-full">
                    <x-input name="from" id="from" type="{{ $toFrom_type }}" placeholder="From"/>
                    <x-input name="to" id="to" type="{{ $toFrom_type }}" placeholder="To"/>
                </div>
            </div>
        @endif

        @if ($toFrom && !empty($filter_items))
            <div class="separator w-0 border-r border-gray-600"></div>
        @endif

        @if (!empty($filter_items))
            <!-- Search Form -->
            <div id="search-form" class="search-box shrink-0 grow w-full">
                <!-- Search Input -->
                <div class="search-input relative">
                    {{-- <x-input name="search_box" id="search_box" oninput="searchData(this.value)" placeholder="🔍 Search {{ $heading }}..." withButton btnId="filter-btn" btnClass="dropdown-trigger" btnText='<i class="text-xs fa-solid fa-filter"></i>' /> --}}
                    <button id="filter-btn" type="button" class="dropdown-trigger bg-[var(--primary-color)] px-3 py-2.5 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer flex gap-2 items-center font-semibold">
                        <i class="text-xs fa-solid fa-filter"></i>
                    </button>
                    <div class="dropdownMenu text-sm absolute mt-2 top-10 right-0 hidden border border-gray-600 w-48 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-2xl opacity-0 transform scale-90 transition-all duration-300 ease-in-out z-50">
                        <ul class="p-2 space-y-1">
                            @foreach ($filter_items as $key => $filter_item)
                                <li>
                                    <label 
                                        class="flex items-center justify-between cursor-pointer group py-2 px-3 hover:bg-[var(--h-bg-color)] rounded-lg transition-all duration-200 ease-in-out relative overflow-hidden"
                                        onclick='setFilter("{{ $key }}")'>
                                        <!-- Hidden input for peer -->
                                        <input 
                                            type="radio" 
                                            name="filter" 
                                            value="{{ $key }}" 
                                            class="hidden peer" 
                                            {{ $key == 'all' ? 'checked' : '' }}
                                        />

                                        <!-- border -->
                                        <div class="absolute left-0 top-0 h-full w-full rounded-lg border-b border-transparent transition-all duration-200 ease-in-out peer-checked:bg-[var(--bg-color)] peer-checked:border-gray-600">
                                        </div>

                                        <!-- Left bar -->
                                        <div class="absolute -left-1 top-1/2 -translate-y-1/2 h-[65%] bg-[var(--primary-color)] rounded-tr-md rounded-br-md transition-all duration-200 ease-in-out
                                            w-0 peer-checked:w-[0.7rem] group-hover:w-[0.6rem] mx-auto">
                                        </div>

                                        <!-- Label Text -->
                                        <span class="peer-checked:text-[var(--primary-color)] peer-checked:ml-1.5 group-hover:text-[var(--primary-color)] group-hover:ml-1 transition-all duration-200 ease-in-out z-10">{{ $filter_item }}</span>

                                        <!-- Selection Dot -->
                                        <div class="w-3 h-3 border-2 border-gray-500 rounded-full flex items-center justify-center peer-checked:border-[var(--primary-color)] group-hover:border-[var(--primary-color)] peer-checked:scale-[1.1] p-[0.1rem] transition-all duration-200 ease-in-out z-10"></div>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if ($link)
            <!-- link_in_header -->
            <div id="link_in_header" class="link_in_headerrom-box flex items-center gap-4 shrink-0">
                <a type="button" href="{{ $linkHref }}" class="bg-[var(--primary-color)] px-4.5 py-1.5 rounded-lg hover:bg-[var(--h-primary-color)] transition-all duration-300 ease-in-out cursor-pointer text-nowrap flex items-center">{{ $linkText }}</a>
            </div>
        @endif
    </div>
</div>

<hr class="border-gray-600 my-4 w-full">