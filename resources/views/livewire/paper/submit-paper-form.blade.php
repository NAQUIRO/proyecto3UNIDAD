<div>
    <form wire:submit="save" class="space-y-6">
        @csrf

        <!-- Título -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Título de la Propuesta <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="title" 
                   wire:model="title" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('title') border-red-500 @enderror"
                   placeholder="Ingrese el título de su propuesta">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Área Temática -->
        <div>
            <label for="thematic_area_id" class="block text-sm font-medium text-gray-700 mb-2">
                Área Temática <span class="text-red-500">*</span>
            </label>
            <select id="thematic_area_id" 
                    wire:model="thematic_area_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('thematic_area_id') border-red-500 @enderror">
                <option value="">Seleccione un área temática</option>
                @foreach($thematicAreas as $area)
                    <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                @endforeach
            </select>
            @error('thematic_area_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Resumen -->
        <div>
            <label for="abstract" class="block text-sm font-medium text-gray-700 mb-2">
                Resumen <span class="text-red-500">*</span>
                <span class="text-sm text-gray-500">(Mínimo 100 palabras, máximo {{ $word_limit }} palabras)</span>
            </label>
            <textarea id="abstract" 
                      wire:model.live="abstract" 
                      rows="8"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('abstract') border-red-500 @enderror"
                      placeholder="Escriba el resumen de su propuesta..."></textarea>
            <div class="mt-2 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Palabras: <span class="font-semibold {{ $wordCount > $word_limit ? 'text-red-600' : 'text-green-600' }}">{{ $wordCount }}</span> / {{ $word_limit }}
                </p>
            </div>
            @error('abstract')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Palabras Clave -->
        <div>
            <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                Palabras Clave
            </label>
            <input type="text" 
                   id="keywords" 
                   wire:model="keywords" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('keywords') border-red-500 @enderror"
                   placeholder="Separadas por comas (ej: IA, Machine Learning, Deep Learning)">
            <p class="mt-1 text-sm text-gray-500">Separe las palabras clave con comas</p>
            @error('keywords')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Editorial (Opcional) -->
        <div>
            <label for="editorial_id" class="block text-sm font-medium text-gray-700 mb-2">
                Editorial (Opcional)
            </label>
            <select id="editorial_id" 
                    wire:model="editorial_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Sin editorial específica</option>
                @foreach($editorials as $editorial)
                    <option value="{{ $editorial['id'] }}">{{ $editorial['name'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Límite de Palabras -->
        <div>
            <label for="word_limit" class="block text-sm font-medium text-gray-700 mb-2">
                Límite de Palabras
            </label>
            <input type="number" 
                   id="word_limit" 
                   wire:model="word_limit" 
                   min="100" 
                   max="5000"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Límite máximo de palabras para el resumen (por defecto: 500)</p>
        </div>

        <!-- Video URL (Opcional) -->
        <div>
            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                URL de Video (Opcional)
            </label>
            <input type="url" 
                   id="video_url" 
                   wire:model="video_url" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('video_url') border-red-500 @enderror"
                   placeholder="https://youtube.com/watch?v=...">
            @error('video_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Archivos -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Archivos Adjuntos</h3>

            <!-- Archivo de Resumen -->
            <div>
                <label for="abstract_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Archivo de Resumen (PDF, DOC, DOCX - Máx. 10MB)
                </label>
                <input type="file" 
                       id="abstract_file" 
                       wire:model="abstract_file"
                       accept=".pdf,.doc,.docx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('abstract_file') border-red-500 @enderror">
                @error('abstract_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($abstract_file)
                    <p class="mt-1 text-sm text-gray-600">Archivo seleccionado: {{ $abstract_file->getClientOriginalName() }}</p>
                @endif
            </div>

            <!-- Archivo Completo -->
            <div>
                <label for="full_paper_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Paper Completo (PDF, DOC, DOCX - Máx. 50MB)
                </label>
                <input type="file" 
                       id="full_paper_file" 
                       wire:model="full_paper_file"
                       accept=".pdf,.doc,.docx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('full_paper_file') border-red-500 @enderror">
                @error('full_paper_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($full_paper_file)
                    <p class="mt-1 text-sm text-gray-600">Archivo seleccionado: {{ $full_paper_file->getClientOriginalName() }}</p>
                @endif
            </div>

            <!-- Archivo de Presentación -->
            <div>
                <label for="presentation_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Presentación (PDF, PPT, PPTX - Máx. 50MB)
                </label>
                <input type="file" 
                       id="presentation_file" 
                       wire:model="presentation_file"
                       accept=".pdf,.ppt,.pptx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('presentation_file') border-red-500 @enderror">
                @error('presentation_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($presentation_file)
                    <p class="mt-1 text-sm text-gray-600">Archivo seleccionado: {{ $presentation_file->getClientOriginalName() }}</p>
                @endif
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4 pt-4">
            <a href="{{ route('paper.index', $congress) }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                {{ $paper ? 'Actualizar' : 'Guardar' }} Propuesta
            </button>
        </div>
    </form>

    @if(session()->has('success'))
        <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
</div>
