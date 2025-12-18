@extends('layouts.public')

@section('title', $congress->title . ' | EventHub')

@section('content')
    <section style="padding-top: 40px;">
        <div class="container">
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('public.congresses.index') }}" style="color: #0a3d62; text-decoration: none; font-weight: 500;">
                    ← Volver a congresos
                </a>
            </div>

            <div style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                @if($congress->banner)
                    <img src="{{ asset('storage/' . $congress->banner) }}" alt="{{ $congress->title }}" style="width: 100%; height: 400px; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #0a3d62 0%, #145374 100%); display: flex; align-items: center; justify-content: center;">
                        <h1 style="color: #fff; font-size: 2.5rem; text-align: center;">{{ $congress->title }}</h1>
                    </div>
                @endif

                <div style="padding: 2.5rem;">
                    @if($congress->logo)
                        <div style="margin-bottom: 1.5rem;">
                            <img src="{{ asset('storage/' . $congress->logo) }}" alt="Logo {{ $congress->title }}" style="max-height: 80px; width: auto;">
                        </div>
                    @endif

                    <h1 style="font-size: 2.5rem; color: #0a3d62; margin-bottom: 1rem;">{{ $congress->title }}</h1>

                    <div style="margin-bottom: 2rem;">
                        @if($congress->thematicAreas->count() > 0)
                            <div style="margin-bottom: 1rem;">
                                @foreach($congress->thematicAreas as $area)
                                    <span style="display: inline-block; background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 20px; font-size: 0.9em; margin-right: 8px; margin-bottom: 8px;">
                                        {{ $area->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <p style="color: #666; font-size: 1.1em; margin-bottom: 0.5rem;">
                            <strong>Fechas:</strong> {{ $congress->start_date->format('d/m/Y') }} - {{ $congress->end_date->format('d/m/Y') }}
                        </p>
                        @if($congress->url)
                            <p style="color: #666; font-size: 1.1em;">
                                <strong>URL:</strong> <a href="{{ $congress->url }}" target="_blank" style="color: #0a3d62; text-decoration: underline;">{{ $congress->url }}</a>
                            </p>
                        @endif
                    </div>

                    @if($congress->description)
                        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                            <h2 style="font-size: 1.5rem; color: #0a3d62; margin-bottom: 1rem;">Descripción</h2>
                            <p style="color: #555; line-height: 1.8; white-space: pre-line;">{{ $congress->description }}</p>
                        </div>
                    @endif

                    @if($congress->url)
                        <div style="margin-top: 2rem;">
                            <a href="{{ $congress->url }}" target="_blank" style="display: inline-block; background-color: #0a3d62; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 1.1em;">
                                Acceder al Micrositio del Congreso
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
