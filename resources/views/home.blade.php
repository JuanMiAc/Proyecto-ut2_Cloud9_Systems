@extends('layouts.app')

@section('title', 'Índice — Cloud 9 Systems')

@section('content')
    <section class="mb-8 text-center">
        <h1 class="mb-2 text-3xl font-bold text-slate-900">Cloud 9 Systems</h1>
        <p class="text-slate-600">Plataforma de gestión cloud · productos, proyectos, arquitecturas y presupuestos</p>
    </section>

    <div class="home-grid">
        <a href="{{ route('productos.index') }}" class="home-tile confirm-nav">
            <div class="text-2xl">📦</div>
            <h3>Catálogo</h3>
            <p>Consultar, crear y gestionar productos cloud.</p>
        </a>
        <a href="{{ route('carrito.index') }}" class="home-tile confirm-nav">
            <div class="text-2xl">🛒</div>
            <h3>Carrito</h3>
            <p>Configurar una selección de seis categorías.</p>
        </a>
        <a href="{{ route('proyectos.index') }}" class="home-tile confirm-nav">
            <div class="text-2xl">📁</div>
            <h3>Proyecto</h3>
            <p>Crear y administrar proyectos y requisitos.</p>
        </a>
        <a href="{{ route('arquitecturas.index') }}" class="home-tile confirm-nav">
            <div class="text-2xl">🧱</div>
            <h3>Arquitectura</h3>
            <p>Consultar arquitecturas guardadas.</p>
        </a>
        <a href="{{ route('presupuestos.index') }}" class="home-tile confirm-nav">
            <div class="text-2xl">📄</div>
            <h3>Presupuesto</h3>
            <p>Consultar presupuestos emitidos.</p>
        </a>
    </div>
@endsection
