@extends('layouts.public')

@section('title', 'EventHub | Congresos Disponibles')

@section('content')
    <section class="eventos" style="padding-top: 40px;">
        <div class="container">
            <h2>Congresos Disponibles</h2>
            
            <!-- Filtros -->
            <div style="background: #fff; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <form method="GET" action="{{ route('public.congresses.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                    <div>
                        <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Buscar</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Buscar por título..."
                            style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-family: 'Poppins', sans-serif;">
                    </div>
                    <div>
                        <label for="thematic_area" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Área Temática</label>
                        <select name="thematic_area" id="thematic_area"
                            style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-family: 'Poppins', sans-serif;">
                            <option value="">Todas las áreas</option>
                            @foreach($thematicAreas as $area)
                                <option value="{{ $area->id }}" {{ request('thematic_area') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" style="width: 100%; background-color: #0a3d62; color: #fff; padding: 0.75rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            Filtrar
                        </button>
                    </div>
                    @if(request()->anyFilled(['search', 'thematic_area']))
                        <div>
                            <a href="{{ route('public.congresses.index') }}" style="display: block; text-align: center; background-color: #666; color: #fff; padding: 0.75rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                Limpiar
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Listado de Congresos -->
            @if($congresses->count() > 0)
                <div class="cards">
                    @foreach($congresses as $congress)
                        <div class="card">
                            @if($congress->banner)
                                <img src="{{ asset('storage/' . $congress->banner) }}" alt="{{ $congress->title }}">
                            @else
                                <img src="{{ asset('img/eventos/evento1.jpg') }}" alt="{{ $congress->title }}">
                            @endif
                            <h3>{{ $congress->title }}</h3>
                            <p>
                                @if($congress->thematicAreas->count() > 0)
                                    @foreach($congress->thematicAreas as $area)
                                        <span style="display: inline-block; background: #e3f2fd; color: #1976d2; padding: 2px 8px; border-radius: 12px; font-size: 0.85em; margin-right: 5px;">{{ $area->name }}</span>
                                    @endforeach
                                    <br>
                                @endif
                                {{ $congress->start_date->format('d/m/Y') }} - {{ $congress->end_date->format('d/m/Y') }}
                                @if($congress->url)
                                    <br><small style="color: #666;">{{ $congress->url }}</small>
                                @endif
                            </p>
                            <div style="padding: 0 15px 15px;">
                                <a href="{{ route('public.congresses.show', $congress->slug) }}" style="display: inline-block; background-color: #0a3d62; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 10px;">
                                    Ver más
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 2rem; text-align: center;">
                    {{ $congresses->links() }}
                </div>
            @else
                <div style="background: #fff; padding: 3rem; border-radius: 12px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <p style="color: #666; font-size: 1.1em;">No se encontraron congresos con los filtros seleccionados.</p>
                    <a href="{{ route('public.congresses.index') }}" style="display: inline-block; margin-top: 1rem; color: #0a3d62; text-decoration: underline;">
                        Ver todos los congresos
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
