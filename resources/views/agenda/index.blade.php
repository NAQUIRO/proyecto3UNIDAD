@extends('layouts.public')

@section('title', 'Agenda - ' . $congress->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-calendar-alt"></i> Agenda del Congreso
            </h1>
            <p class="lead">{{ $congress->title }}</p>
        </div>
    </div>

    @livewire('agenda.agenda-view', ['congress' => $congress])
</div>
@endsection

