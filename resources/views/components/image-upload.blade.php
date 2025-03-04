<div class="grid grid-cols-1 md:grid-cols-1">
    <label for="{{ $id }}"
        class="border-dashed border-2 border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-primary transition-all duration-300 ease-in-out relative">
        <input id="{{ $id }}" type="file" name="{{ $name }}" accept="image/*"
            class="opacity-0 absolute inset-0 cursor-pointer"
            onchange="previewImage(event, '{{ $id }}')" />
        <div id="image_preview_{{ $id }}" class="flex flex-col items-center max-w-[50%]">
            <img src="{{ $placeholder }}" alt="Upload Icon"
                class="w-16 h-16 mb-2" id="placeholder_icon_{{ $id }}" />
            <p id="upload_text_{{ $id }}" class="text-md text-gray-500">{{ $uploadText }}</p>
        </div>
        @error($name)
            <div class="text-[--border-error] mt-1">{{ $message }}</div>
        @enderror
    </label>
</div>

@push('scripts')
<script>
    // 🖼️ Reusable Preview Image Function
    function previewImage(event, id) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById(`image_preview_${id}`);
        const placeholderIcon = document.getElementById(`placeholder_icon_${id}`);
        const uploadText = document.getElementById(`upload_text_${id}`);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove placeholder and text
                placeholderIcon.style.display = 'none';
                uploadText.style.display = 'none';

                // Create or update image preview
                let imgPreview = document.getElementById(`img_preview_${id}`);
                if (!imgPreview) {
                    imgPreview = document.createElement('img');
                    imgPreview.id = `img_preview_${id}`;
                    imgPreview.className = 'w-32 h-32 object-cover rounded-lg mt-2'; // Styling for preview
                    previewContainer.appendChild(imgPreview);
                }
                imgPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Restore placeholder if no file is selected
            placeholderIcon.style.display = 'block';
            uploadText.style.display = 'block';
            const imgPreview = document.getElementById(`img_preview_${id}`);
            if (imgPreview) {
                imgPreview.remove();
            }
        }
    }
</script>
@endpush