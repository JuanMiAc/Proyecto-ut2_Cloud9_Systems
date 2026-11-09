@php
    $isEdit = isset($proyecto);
    $requisitosOld = old('requisitos', $isEdit ? $proyecto->requisitos->pluck('requisito')->all() : []);
@endphp

<div class="grid gap-5" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <div class="card overflow-hidden">
        <div class="card-header">{{ $isEdit ? 'Editar proyecto' : 'Crear nuevo proyecto' }}</div>
        <form method="POST" action="{{ $isEdit ? route('proyectos.update', $proyecto) : route('proyectos.store') }}" class="space-y-5 p-5" id="proyecto-form">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <div class="mb-2 flex items-center gap-2 font-semibold">
                    <span class="step-badge">1</span> Nombre del proyecto *
                </div>
                <input type="text" name="nombre" value="{{ old('nombre', $proyecto->nombre ?? '') }}" class="form-input" maxlength="150" required>
            </div>

            <div>
                <div class="mb-2 flex items-center gap-2 font-semibold">
                    <span class="step-badge">2</span> Descripción *
                </div>
                <textarea name="descripcion" class="form-textarea" rows="3" required>{{ old('descripcion', $proyecto->descripcion ?? '') }}</textarea>
            </div>

            <div>
                <div class="mb-2 flex items-center gap-2 font-semibold">
                    <span class="step-badge">3</span> Presupuesto (entero) *
                </div>
                <input type="number" name="presupuesto" value="{{ old('presupuesto', $proyecto->presupuesto ?? 0) }}" class="form-input" min="0" step="1" required>
            </div>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="estado" value="1" @checked(old('estado', $proyecto->estado ?? true))>
                Proyecto activo
            </label>

            <div>
                <div class="mb-2 flex items-center gap-2 font-semibold">
                    <span class="step-badge">4</span> Requisitos
                </div>
                <p class="mb-2 text-sm text-slate-600">Añada los requisitos que necesita su proyecto.</p>
                <div class="mb-3 flex flex-wrap gap-2">
                    <input type="text" id="requisito-input" class="form-input" style="flex:1;min-width:220px" maxlength="255" placeholder="Ejemplo: Alta disponibilidad...">
                    <button type="button" id="requisito-add" class="btn btn-success">+ Añadir</button>
                </div>
                <div id="requisitos-hidden"></div>
            </div>

            <div class="info-box">Los requisitos se guardan con el proyecto al pulsar Terminar / Guardar.</div>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Guardar cambios' : 'Terminar' }}</button>
                <a href="{{ route('proyectos.index') }}" class="btn btn-secondary confirm-nav">Cancelar</a>
            </div>
        </form>
    </div>

    <aside class="card p-4 h-fit sticky top-24">
        <h3 class="mt-0 mb-3 text-lg font-semibold">Requisitos añadidos</h3>
        <div id="requisitos-lista" class="flex flex-col gap-2">
            <p class="sin-requisitos text-sm text-slate-500">Todavía no hay requisitos añadidos.</p>
        </div>
    </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const initial = @json($requisitosOld);
    const lista = document.getElementById('requisitos-lista');
    const hidden = document.getElementById('requisitos-hidden');
    const input = document.getElementById('requisito-input');
    const addBtn = document.getElementById('requisito-add');
    let requisitos = Array.isArray(initial) ? [...initial] : [];

    function render() {
        hidden.innerHTML = '';
        if (requisitos.length === 0) {
            lista.innerHTML = '<p class="sin-requisitos text-sm text-slate-500">Todavía no hay requisitos añadidos.</p>';
            return;
        }
        lista.innerHTML = '';
        requisitos.forEach((texto, index) => {
            const item = document.createElement('div');
            item.className = 'req-item';
            item.innerHTML = `<span>${texto.replace(/</g,'&lt;')}</span>`;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-danger';
            btn.textContent = 'Quitar';
            btn.addEventListener('click', () => {
                requisitos.splice(index, 1);
                render();
            });
            item.appendChild(btn);
            lista.appendChild(item);

            const field = document.createElement('input');
            field.type = 'hidden';
            field.name = 'requisitos[]';
            field.value = texto;
            hidden.appendChild(field);
        });
    }

    addBtn.addEventListener('click', () => {
        const value = input.value.trim();
        if (!value) return;
        requisitos.push(value);
        input.value = '';
        render();
    });

    render();
});
</script>
