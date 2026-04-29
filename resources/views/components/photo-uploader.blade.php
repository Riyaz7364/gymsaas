@props(['name' => 'photos[]', 'label' => 'Photos (Optional)', 'existing' => []])

<div style="grid-column:1/-1;" x-data="photoUploader(@js($existing))">
    <label class="gh-label">{{ $label }}</label>
    
    <!-- Custom Dropzone -->
    <div 
        class="flex flex-col items-center justify-center w-full py-8 px-4 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
        :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; handleDrop($event)"
        @click="$refs.fileInput.click()"
    >
        <div class="flex flex-col items-center justify-center text-center pointer-events-none">
            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="mb-2 text-sm text-gray-600"><span class="font-semibold text-blue-600">Click to upload</span> or drag and drop</p>
            <p class="text-xs text-gray-500">PNG, JPG, GIF, or WEBP — max 2MB per image</p>
        </div>
        <input x-ref="fileInput" type="file" name="{{ $name }}" class="hidden" multiple accept="image/*" @change="handleFiles($event.target.files)">
    </div>

    <!-- Image Previews Grid -->
    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" x-show="files.length > 0 || existingFiles.length > 0" style="display: none;">
        <!-- Existing Images -->
        <template x-for="(file, index) in existingFiles" :key="'existing-'+file.id">
            <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-white" style="aspect-ratio: 1/1;">
                <img :src="file.url" style="width: 100%; height: 100%; object-fit: cover;" />
                <button type="button" @click.stop="removeExisting(index, file.id)" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-600" title="Remove image">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>
        
        <!-- New Uploads -->
        <template x-for="(file, index) in files" :key="index">
            <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-white" style="aspect-ratio: 1/1;">
                <img :src="file.preview" style="width: 100%; height: 100%; object-fit: cover;" />
                <button type="button" @click.stop="removeFile(index)" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-600" title="Remove image">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Hidden inputs to track deleted existing photos -->
    <template x-for="id in deletedExisting" :key="id">
        <input type="hidden" name="delete_existing_photos[]" :value="id">
    </template>
</div>

@once
@push('scripts')
<script>
    function photoUploader(existing = []) {
        return {
            isDragging: false,
            files: [],
            existingFiles: existing,
            deletedExisting: [],
            handleDrop(event) {
                if (event.dataTransfer && event.dataTransfer.files) this.handleFiles(event.dataTransfer.files);
            },
            handleFiles(fileList) {
                const dt = new DataTransfer();
                this.files.forEach(f => dt.items.add(f.file));
                for (let i = 0; i < fileList.length; i++) {
                    if (fileList[i].type.startsWith('image/')) {
                        dt.items.add(fileList[i]);
                        this.files.push({ file: fileList[i], preview: URL.createObjectURL(fileList[i]) });
                    }
                }
                this.$refs.fileInput.files = dt.files;
            },
            removeFile(index) {
                URL.revokeObjectURL(this.files[index].preview);
                this.files.splice(index, 1);
                const dt = new DataTransfer();
                this.files.forEach(f => dt.items.add(f.file));
                this.$refs.fileInput.files = dt.files;
            },
            removeExisting(index, id) {
                this.deletedExisting.push(id);
                this.existingFiles.splice(index, 1);
            }
        };
    }
</script>
@endpush
@endonce