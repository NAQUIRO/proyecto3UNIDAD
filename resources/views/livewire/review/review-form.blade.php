<div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Revisión de Paper</h2>
        
        <!-- Información del Paper (Doble Ciego - Sin información del autor) -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $paper->title }}</h3>
            <p class="text-sm text-gray-600 mb-2">
                <strong>Área Temática:</strong> {{ $paper->thematicArea->name ?? 'N/A' }}
            </p>
            <p class="text-sm text-gray-600">
                <strong>Resumen:</strong>
            </p>
            <p class="text-gray-700 mt-2">{{ $paper->abstract }}</p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <!-- Recomendación -->
            <div>
                <label for="recommendation" class="block text-sm font-medium text-gray-700 mb-2">
                    Recomendación <span class="text-red-500">*</span>
                </label>
                <select id="recommendation" 
                        wire:model="recommendation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('recommendation') border-red-500 @enderror">
                    <option value="">Seleccione una recomendación</option>
                    <option value="accept">Aceptar</option>
                    <option value="revision_required">Requerir Revisión</option>
                    <option value="reject">Rechazar</option>
                </select>
                @error('recommendation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rúbricas de Evaluación -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Criterios de Evaluación</h3>
                
                @foreach($rubrics as $index => $rubric)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="mb-3">
                            <h4 class="font-semibold text-gray-800">{{ $rubric['criterion'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $rubric['description'] }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Puntuación (0 - {{ $rubric['max_score'] }})
                            </label>
                            <input type="number" 
                                   wire:model.live="rubrics.{{ $index }}.score"
                                   min="0" 
                                   max="{{ $rubric['max_score'] }}"
                                   step="0.5"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('rubrics.' . $index . '.score') border-red-500 @enderror">
                            @error('rubrics.' . $index . '.score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Comentarios (Opcional)
                            </label>
                            <textarea wire:model="rubrics.{{ $index }}.comments"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Comentarios sobre este criterio..."></textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Comentarios Generales -->
            <div>
                <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                    Comentarios Generales <span class="text-red-500">*</span>
                    <span class="text-sm text-gray-500">(Mínimo 50 caracteres - Visibles para el autor)</span>
                </label>
                <textarea id="comments" 
                          wire:model="comments" 
                          rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('comments') border-red-500 @enderror"
                          placeholder="Escriba sus comentarios sobre el paper..."></textarea>
                @error('comments')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Comentarios Confidenciales -->
            <div>
                <label for="confidential_comments" class="block text-sm font-medium text-gray-700 mb-2">
                    Comentarios Confidenciales
                    <span class="text-sm text-gray-500">(No visibles para el autor - Solo para el comité)</span>
                </label>
                <textarea id="confidential_comments" 
                          wire:model="confidential_comments" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="Comentarios confidenciales para el comité científico..."></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('review.index', $paper->congress) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    Completar Revisión
                </button>
            </div>
        </form>
    </div>

    @if(session()->has('success'))
        <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
</div>
