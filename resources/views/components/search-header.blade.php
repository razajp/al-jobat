@props([
    'heading' => '',
    'filter_items' => [],
])

<div class="header w-[80%] mx-auto flex items-end justify-between px-6">
    <h5 id="name" class="text-3xl text-[--text-color] capitalize font-semibold leading-none">{{ $heading }}</h5>

    <!-- Search Form -->
    <div id="search-form" class="search-box w-1/3">
        <!-- Search Input -->
        <div class="search-input relative">
            <x-input name="search_box" id="search_box" oninput="searchData(this.value)" placeholder="🔍 Search {{ $heading }}..." withButton btnId="filter-btn" btnClass="dropdown-trigger" btnText='<i class="text-xs fa-solid fa-filter"></i>' />
            <div class="dropdownMenu text-sm absolute mt-2 top-100 right-0 hidden border border-gray-600 w-48 bg-[--h-secondary-bg-color] text-[--text-color] shadow-lg rounded-xl opacity-0 transform scale-95 transition-all duration-300 ease-in-out z-50">
                <ul class="p-2">
                    @foreach ($filter_items as $key => $filter_item)
                        <li>
                            <label class="flex items-center justify-between cursor-pointer group py-2 px-3 hover:bg-[--h-bg-color] rounded-md transition-all duration-200 ease-in-out" onclick='setFilter("{{ $key }}")'>
                                <input type="radio" name="filter" value="{{ $key }}" class="hidden peer" {{ $key == 'all' ? 'checked' : '' }}/>
                                <span class="text-[--text-color] transition-all peer-checked:text-[--primary-color] peer-checked:ml-1">{{ $filter_item }}</span>
                                <div class="w-3 h-3 border-2 border-gray-500 rounded-full flex items-center justify-center peer-checked:border-[--primary-color]">
                                    <div class="w-2.5 h-2.5 bg-[--primary-color] rounded-full scale-0 peer-checked:scale-100 transition-transform"></div>
                                </div>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<hr class="border-gray-600 my-4 w-[80%] mx-auto">