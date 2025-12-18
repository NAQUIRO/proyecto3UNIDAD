<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Congreso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.congresses.update', $congress) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $congress->title) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="draft" {{ old('status', $congress->status) == 'draft' ? 'selected' : '' }}>Borrador</option>
                                    <option value="published" {{ old('status', $congress->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                                    <option value="finished" {{ old('status', $congress->status) == 'finished' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $congress->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Inicio *</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $congress->start_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Fin *</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $congress->end_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL del Micrositio</label>
                            <input type="text" name="url" id="url" value="{{ old('url', $congress->url) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="https://congreso.ejemplo.com">
                            @error('url')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo</label>
                                <div class="mb-2">
                                    <div id="logo-preview" class="{{ $congress->logo ? '' : 'hidden' }} mb-2">
                                        <img id="logo-preview-img" src="{{ $congress->logo ? asset('storage/' . $congress->logo) : '' }}" alt="Vista previa del logo" class="h-20 w-auto border border-gray-300 rounded-lg p-2 bg-gray-50">
                                    </div>
                                    <label for="logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click para subir</span> o arrastra y suelta</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF o SVG (MAX. 2MB)</p>
                                        </div>
                                        <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewImage(this, 'logo-preview', 'logo-preview-img')">
                                    </label>
                                </div>
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="banner" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Banner</label>
                                <div class="mb-2">
                                    <div id="banner-preview" class="{{ $congress->banner ? '' : 'hidden' }} mb-2">
                                        <img id="banner-preview-img" src="{{ $congress->banner ? asset('storage/' . $congress->banner) : '' }}" alt="Vista previa del banner" class="h-32 w-full object-cover border border-gray-300 rounded-lg">
                                    </div>
                                    <label for="banner" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click para subir</span> o arrastra y suelta</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF o SVG (MAX. 5MB)</p>
                                        </div>
                                        <input type="file" name="banner" id="banner" accept="image/*" class="hidden" onchange="previewImage(this, 'banner-preview', 'banner-preview-img')">
                                    </label>
                                </div>
                                @error('banner')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <script>
                            function previewImage(input, previewId, imgId) {
                                if (input.files && input.files[0]) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        document.getElementById(imgId).src = e.target.result;
                                        document.getElementById(previewId).classList.remove('hidden');
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }

                            // Drag and drop functionality
                            ['logo', 'banner'].forEach(id => {
                                const dropZone = document.querySelector(`label[for="${id}"]`);
                                const input = document.getElementById(id);
                                
                                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                                    dropZone.addEventListener(eventName, preventDefaults, false);
                                });

                                function preventDefaults(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                }

                                ['dragenter', 'dragover'].forEach(eventName => {
                                    dropZone.addEventListener(eventName, () => {
                                        dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
                                    }, false);
                                });

                                ['dragleave', 'drop'].forEach(eventName => {
                                    dropZone.addEventListener(eventName, () => {
                                        dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
                                    }, false);
                                });

                                dropZone.addEventListener('drop', (e) => {
                                    const dt = e.dataTransfer;
                                    const files = dt.files;
                                    input.files = files;
                                    previewImage(input, `${id}-preview`, `${id}-preview-img`);
                                }, false);
                            });
                        </script>

                        <div class="mb-4">
                            <label for="thematic_areas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Áreas Temáticas</label>
                            <select name="thematic_areas[]" id="thematic_areas" multiple
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @foreach($thematicAreas as $area)
                                    <option value="{{ $area->id }}" {{ in_array($area->id, old('thematic_areas', $congress->thematicAreas->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples áreas</p>
                            @error('thematic_areas')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Descripción</label>
                                <textarea name="meta_description" id="meta_description" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('meta_description', $congress->meta_description) }}</textarea>
                                @error('meta_description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $congress->meta_keywords) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="palabra1, palabra2, palabra3">
                                @error('meta_keywords')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.congresses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Congreso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
