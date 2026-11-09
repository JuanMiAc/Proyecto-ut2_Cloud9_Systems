@extends('layouts.app')

@section('title', 'Editar proyecto — Cloud 9 Systems')

@section('content')
    <div class="mb-4">
        <a href="{{ route('proyectos.index') }}" class="btn btn-secondary confirm-nav">← Volver a proyectos</a>
    </div>
    @include('proyectos._form', ['proyecto' => $proyecto])
@endsection
