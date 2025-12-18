<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $congress->title }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.congresses.edit', $congress) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('admin.congresses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        @if($congress->logo)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo</label>
                                <img src="{{ asset('storage/' . $congress->logo) }}" alt="Logo" class="max-h-32">
                            </div>
                        @endif
                        @if($congress->banner)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Banner</label>
                                <img src="{{ asset('storage/' . $congress->banner) }}" alt="Banner" class="max-h-32">
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $congress->description ?? 'Sin descripción' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                            <p class="mt-1">
                                @if($congress->status === 'published')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Publicado
                                    </span>
                                @elseif($congress->status === 'draft')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Borrador
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        Finalizado
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fechas</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $congress->start_date->format('d/m/Y') }} - {{ $congress->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL del Micrositio</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">
                            @if($congress->url)
                                <a href="{{ $congress->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ $congress->url }}
                                </a>
                            @else
                                Sin URL configurada
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Áreas Temáticas</label>
                        <div class="mt-1 flex flex-wrap gap-2">
                            @forelse($congress->thematicAreas as $area)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $area->name }}
                                </span>
                            @empty
                                <span class="text-gray-500 dark:text-gray-400">Sin áreas temáticas asignadas</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Descripción</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $congress->meta_description ?? 'Sin meta descripción' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Keywords</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $congress->meta_keywords ?? 'Sin meta keywords' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Creado por</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $congress->creator->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Creación</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $congress->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
