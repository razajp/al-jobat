<div class="grid grid-cols-1 md:grid-cols-1">
    <label for="{{ $id }}"
        class="border-dashed border-2 border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-primary transition-all duration-300 ease-in-out relative">
        <input id="{{ $id }}" type="file" name="{{ $name }}" accept="image/*"
            class="image_upload opacity-0 absolute inset-0 cursor-pointer"
            onchange="previewImage(event)" />
        <div id="image_preview_{{ $id }}" class="flex flex-col items-center max-w-[50%]">
            <img src="{{ $placeholder }}" alt="Upload Icon"
                class="placeholder_icon w-16 h-16 mb-2 rounded-md" id="placeholder_icon_{{ $id }}" />
            <p id="upload_text_{{ $id }}" class="upload_text text-md text-gray-500">{{ $uploadText }}</p>
        </div>
        @error($name)
            <div class="text-[--border-error] mt-1">{{ $message }}</div>
        @enderror
    </label>
</div>