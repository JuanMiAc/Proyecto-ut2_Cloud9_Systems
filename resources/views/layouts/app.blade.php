<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cloud 9 Systems')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @php
        $navItems = [
            ['route' => 'home', 'label' => 'Índice', 'match' => ['home']],
            ['route' => 'productos.index', 'label' => 'Catálogo', 'match' => ['productos.*']],
            ['route' => 'carrito.index', 'label' => 'Carrito', 'match' => ['carrito.*']],
            ['route' => 'proyectos.index', 'label' => 'Proyecto', 'match' => ['proyectos.*']],
            ['route' => 'arquitecturas.index', 'label' => 'Arquitectura', 'match' => ['arquitecturas.*']],
            ['route' => 'presupuestos.index', 'label' => 'Presupuesto', 'match' => ['presupuestos.*']],
        ];
    @endphp

    <header class="top-nav sticky top-0 z-40">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('home') }}" class="confirm-nav flex items-center gap-2 text-white no-underline" data-confirm-nav="1">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-400/30 text-lg">☁</span>
                <strong class="text-base tracking-wide">Cloud 9 Systems</strong>
            </a>
            <nav class="flex flex-wrap items-center gap-1">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['match']); @endphp
                    @if ($active)
                        <span class="nav-link is-active" aria-current="page">{{ $item['label'] }}</span>
                    @else
                        <a href="{{ route($item['route']) }}" class="nav-link confirm-nav" data-confirm-nav="1">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul class="m-0 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <div id="detail-modal" class="modal-backdrop" aria-hidden="true">
        <div class="modal-panel">
            <div class="modal-header">
                <h2 id="detail-modal-title" class="m-0 text-lg font-semibold text-slate-800">Detalle</h2>
                <button type="button" id="detail-modal-close" class="btn btn-secondary">Cerrar</button>
            </div>
            <div id="detail-modal-body" class="modal-body text-sm text-slate-700"></div>
        </div>
    </div>

    <script>
        window.Cloud9 = {
            clearCartUrl: @json(route('carrito.limpiar')),
            csrfToken: @json(csrf_token()),
            regionesUrl: @json(route('api.regiones')),
            tiposUrl: @json(route('api.tipos')),
            productosUrl: @json(route('api.productos')),
            carritoAnadirUrl: @json(route('carrito.anadir')),
        };
    </script>
    <script src="{{ asset('js/app.js') }}?v=2" defer></script>
</body>
</html>
