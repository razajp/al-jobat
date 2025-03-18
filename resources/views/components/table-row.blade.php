@props([
    "id" => "",
    "data_json" => "",
    "cols" => [],
    "onclick" => "toggleDetails(this)"
])

<div id="{{ $id }}" data-json='{{ $data_json }}'
    class="contextMenuToggle modalToggle relative group grid grid-cols-4 text-center border-b border-[--h-bg-color] items-center py-2 cursor-pointer hover:bg-[--h-secondary-bg-color] transition-all fade-in ease-in-out"
    onclick="{{ $onclick }}">
    @foreach ($cols as $col)
        <span>{{ $col }}</span>
    @endforeach
</div>