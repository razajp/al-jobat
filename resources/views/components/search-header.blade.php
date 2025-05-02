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

    @if (!empty($filter_items))
        <!-- Search Form -->
        <div id="search-form" class="search-box w-1/3">
            <!-- Search Input -->
            <div class="search-input relative">
                <x-input name="search_box" id="search_box" oninput="searchData(this.value)" placeholder="🔍 Search {{ $heading }}..." withButton btnId="filter-btn" btnClass="dropdown-trigger" btnText='<i class="text-xs fa-solid fa-filter"></i>' />
                <div class="dropdownMenu text-sm absolute mt-2 top-10 right-0 hidden border border-gray-600 w-48 bg-[var(--h-secondary-bg-color)] text-[var(--text-color)] shadow-lg rounded-2xl opacity-0 transform scale-95 transition-all duration-300 ease-in-out z-50">
                    <ul class="p-2">
                        @foreach ($filter_items as $key => $filter_item)
                            <li>
                                <label class="flex items-center justify-between cursor-pointer group py-2 px-3 hover:bg-[var(--h-bg-color)] grop2 rounded-lg transition-all duration-200 ease-in-out" onclick='setFilter("{{ $key }}")'>
                                    <input type="radio" name="filter" value="{{ $key }}" class="hidden peer" {{ $key == 'all' ? 'checked' : '' }}/>
                                    <span class="transition-all peer-checked:text-[var(--primary-color)] peer-checked:ml-1 grop2-hover:text-[var(--primary-color)]">{{ $filter_item }}</span>
                                    <div class="w-3 h-3 border-2 border-gray-500 rounded-full flex items-center justify-center peer-checked:border-[var(--primary-color)]">
                                        <div class="w-2.5 h-2.5 bg-[var(--primary-color)] rounded-full scale-0 peer-checked:scale-100 transition-transform"></div>
                                    </div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    
    @if ($toFrom)
        <!-- toFrom -->
        <div id="toFrom" class="toFrom-box w-1/3 flex items-center gap-4">
            <label for="to" class="block font-medium text-[var(--secondary-text)] grow text-nowrap">{{ $toFrom_label }}</label>
            <div class="toFrom-inputs relative grid grid-cols-2 gap-4 w-full">
                <x-input name="from" id="from" type="{{ $toFrom_type }}" placeholder="From"/>
                <x-input name="to" id="to" type="{{ $toFrom_type }}" placeholder="To"/>
            </div>
        </div>
    @endif

    @if ($link)
        <!-- link_in_header -->
        <div id="link_in_header" class="link_in_headerrom-box flex items-center gap-4">
            <a type="button" href="{{ $linkHref }}" class="bg-[var(--primary-color)] px-4.5 py-1.5 rounded-lg hover:bg-[var(--h-primary-color)] transition-all 0.3s ease-in-out cursor-pointer text-nowrap flex items-center">{{ $linkText }}</a>
        </div>
    @endif
</div>

<hr class="border-gray-600 my-4 w-full">