const CONFIRM_MSG = 'Al salir se perderán los datos no guardados. ¿Deseas continuar?';

let allowUnload = false;
let navigationConfirmPending = false;

function clearCart() {
    const url = window.Cloud9?.clearCartUrl;
    const token = window.Cloud9?.csrfToken;
    if (!url || !token) {
        return Promise.resolve();
    }

    return fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    }).catch(() => undefined);
}

function shouldInterceptNavigation(anchor) {
    if (!anchor || anchor.tagName !== 'A') {
        return false;
    }
    if (anchor.hasAttribute('data-skip-confirm')) {
        return false;
    }
    if (anchor.classList.contains('is-active') || anchor.getAttribute('aria-current') === 'page') {
        return false;
    }
    const href = anchor.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
        return false;
    }
    if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
        return false;
    }
    try {
        const url = new URL(anchor.href, window.location.origin);
        return url.origin === window.location.origin;
    } catch {
        return false;
    }
}

document.addEventListener('click', async (event) => {
    const anchor = event.target.closest('a');
    if (!shouldInterceptNavigation(anchor)) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    if (navigationConfirmPending) {
        return;
    }

    navigationConfirmPending = true;
    const accepted = window.confirm(CONFIRM_MSG);
    navigationConfirmPending = false;

    if (!accepted) {
        return;
    }

    allowUnload = true;
    await clearCart();
    window.location.href = anchor.href;
});

document.addEventListener('submit', () => {
    allowUnload = true;
});

// Solo para cierre/recarga de pestaña. No se usa junto a la confirmación interna.
window.addEventListener('beforeunload', (event) => {
    if (allowUnload) {
        return;
    }
    event.preventDefault();
    event.returnValue = '';
});

function openModal(title, html) {
    const modal = document.getElementById('detail-modal');
    const titleEl = document.getElementById('detail-modal-title');
    const bodyEl = document.getElementById('detail-modal-body');
    if (!modal || !titleEl || !bodyEl) {
        return;
    }
    titleEl.textContent = title;
    bodyEl.innerHTML = html;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
}

function closeModal() {
    const modal = document.getElementById('detail-modal');
    if (!modal) {
        return;
    }
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
}

document.getElementById('detail-modal-close')?.addEventListener('click', closeModal);
document.getElementById('detail-modal')?.addEventListener('click', (event) => {
    if (event.target.id === 'detail-modal') {
        closeModal();
    }
});

document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('[data-detail-url]');
    if (!trigger) {
        return;
    }

    event.preventDefault();
    const url = trigger.getAttribute('data-detail-url');
    const title = trigger.getAttribute('data-detail-title') || 'Detalle';

    try {
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        if (!response.ok) {
            throw new Error('No se pudo cargar el detalle');
        }
        const data = await response.json();
        const renderer = trigger.getAttribute('data-detail-type') || 'generic';
        openModal(title, renderDetail(renderer, data));
    } catch (error) {
        openModal('Error', `<p>${escapeHtml(error.message)}</p>`);
    }
});

function renderDetail(type, data) {
    if (type === 'producto') {
        return `
            <dl class="grid gap-2">
                <div><strong>Nombre:</strong> ${escapeHtml(data.nombre)}</div>
                <div><strong>Descripción:</strong> ${escapeHtml(data.descripcion)}</div>
                <div><strong>Proveedor:</strong> ${escapeHtml(data.proveedor)}</div>
                <div><strong>Región:</strong> ${escapeHtml(data.region)}</div>
                <div><strong>Categoría:</strong> ${escapeHtml(data.tipo)}</div>
                <div><strong>Precio:</strong> ${data.precio} €</div>
                <div><strong>Disponible:</strong> ${data.disponible ? 'Sí' : 'No'}</div>
                <div><strong>Protegido:</strong> ${data.protegido ? 'Sí' : 'No'}</div>
            </dl>`;
    }

    if (type === 'proyecto') {
        const requisitos = (data.requisitos || []).map((r) => `<li>${escapeHtml(r)}</li>`).join('') || '<li>Sin requisitos</li>';
        const arquitecturas = (data.arquitecturas || []).map((a) => `<li>${escapeHtml(a.nombre)}</li>`).join('') || '<li>Sin arquitecturas</li>';
        return `
            <dl class="grid gap-2">
                <div><strong>Nombre:</strong> ${escapeHtml(data.nombre)}</div>
                <div><strong>Descripción:</strong> ${escapeHtml(data.descripcion)}</div>
                <div><strong>Presupuesto:</strong> ${data.presupuesto} €</div>
                <div><strong>Estado:</strong> ${data.estado ? 'Activo' : 'Inactivo'}</div>
                <div><strong>Creado:</strong> ${escapeHtml(data.created_at || '')}</div>
                <div><strong>Requisitos:</strong><ul>${requisitos}</ul></div>
                <div><strong>Arquitecturas:</strong><ul>${arquitecturas}</ul></div>
            </dl>`;
    }

    if (type === 'arquitectura' || type === 'presupuesto') {
        const productos = (data.productos || [])
            .map((p) => `<li>${escapeHtml(p.tipo)}: ${escapeHtml(p.nombre)} (${p.precio} €) — ${escapeHtml(p.proveedor)} / ${escapeHtml(p.region)}</li>`)
            .join('');
        const importe = type === 'arquitectura' ? data.importe_total : data.importe;
        return `
            <dl class="grid gap-2">
                <div><strong>Nombre:</strong> ${escapeHtml(data.nombre)}</div>
                ${type === 'arquitectura' ? `<div><strong>Proyecto:</strong> ${escapeHtml(data.proyecto || 'Independiente')}</div>` : ''}
                ${type === 'presupuesto' ? `<div><strong>Tipo de pago:</strong> ${escapeHtml(data.tipo_pago)}</div>` : ''}
                <div><strong>Importe:</strong> ${importe} €</div>
                <div><strong>Fecha:</strong> ${escapeHtml(data.created_at || '')}</div>
                <div><strong>Productos:</strong><ul>${productos}</ul></div>
            </dl>`;
    }

    return `<pre>${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function fillSelect(select, items, placeholder, selectedValue = null) {
    select.innerHTML = '';
    const empty = document.createElement('option');
    empty.value = '';
    empty.textContent = placeholder;
    select.appendChild(empty);

    Object.entries(items || {}).forEach(([id, nombre]) => {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = nombre;
        if (selectedValue !== null && String(selectedValue) === String(id)) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

async function loadRegiones(proveedorSelect, regionSelect, selectedRegion = null) {
    const proveedorId = proveedorSelect.value;
    fillSelect(regionSelect, {}, 'Seleccione región');
    regionSelect.disabled = !proveedorId;

    if (!proveedorId) {
        return;
    }

    const response = await fetch(`${window.Cloud9.regionesUrl}?proveedor_id=${encodeURIComponent(proveedorId)}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    const payload = await response.json();
    fillSelect(regionSelect, payload.regiones || {}, 'Seleccione región', selectedRegion);
    regionSelect.disabled = false;
}

async function loadTipos(proveedorId, regionId, tipoSelect, soloDisponibles, selectedTipo = null) {
    fillSelect(tipoSelect, {}, 'Seleccione categoría');
    tipoSelect.disabled = !(proveedorId && regionId);

    if (!proveedorId || !regionId) {
        return;
    }

    const params = new URLSearchParams({
        proveedor_id: proveedorId,
        region_id: regionId,
    });
    if (soloDisponibles) {
        params.set('solo_disponibles', '1');
    }

    const response = await fetch(`${window.Cloud9.tiposUrl}?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    const payload = await response.json();
    fillSelect(tipoSelect, payload.tipos || {}, 'Seleccione categoría', selectedTipo);
    tipoSelect.disabled = false;
}

function renderCatalogProducts(container, productos) {
    if (!productos.length) {
        container.innerHTML = '<div class="info-box">No hay productos para esa combinación.</div>';
        return;
    }

    const rows = productos.map((producto) => {
        const meta = [
            escapeHtml(producto.proveedor),
            escapeHtml(producto.region),
            escapeHtml(producto.tipo),
            producto.disponible ? null : '<span class="text-orange-600">No disponible</span>',
            producto.protegido ? '<span class="text-sky-700">Protegido</span>' : null,
        ].filter(Boolean).join(' · ');

        let actions = `
            <strong class="text-slate-900">${producto.precio} €</strong>
            <button type="button" class="btn btn-ghost"
                data-detail-url="${escapeHtml(producto.show_url)}"
                data-detail-type="producto"
                data-detail-title="Detalle del producto">Ver detalle</button>`;

        if (producto.puede_editarse) {
            actions += `
                <a href="${escapeHtml(producto.edit_url)}" class="btn btn-secondary confirm-nav">Editar</a>
                <form method="POST" action="${escapeHtml(producto.destroy_url)}" onsubmit="return confirm('¿Eliminar este producto?');">
                    <input type="hidden" name="_token" value="${escapeHtml(window.Cloud9.csrfToken)}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>`;
        }

        return `
            <div class="product-row">
                <div>
                    <div class="font-semibold text-slate-900">${escapeHtml(producto.nombre)}</div>
                    <div class="text-sm text-slate-500">${meta}</div>
                    <div class="mt-1 text-sm text-slate-600">${escapeHtml(producto.descripcion)}</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">${actions}</div>
            </div>`;
    }).join('');

    container.innerHTML = `
        <div class="card p-4">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="m-0 text-lg font-semibold">Productos disponibles</h2>
                <span class="badge badge-muted">${productos.length}</span>
            </div>
            <div class="flex flex-col gap-3">${rows}</div>
        </div>`;
}

function renderCartProducts(container, productos) {
    if (!productos.length) {
        container.innerHTML = '<div class="info-box">No hay productos disponibles para esa combinación.</div>';
        return;
    }

    const rows = productos.map((producto) => `
        <div class="product-row">
            <div>
                <div class="font-semibold">${escapeHtml(producto.nombre)}</div>
                <div class="text-sm text-slate-500">${escapeHtml(producto.descripcion)} · ${producto.precio} €</div>
            </div>
            <form method="POST" action="${escapeHtml(window.Cloud9.carritoAnadirUrl)}">
                <input type="hidden" name="_token" value="${escapeHtml(window.Cloud9.csrfToken)}">
                <input type="hidden" name="producto_id" value="${producto.id}">
                <button type="submit" class="btn btn-primary">Añadir +</button>
            </form>
        </div>`).join('');

    container.innerHTML = `<div class="flex flex-col gap-3">${rows}</div>`;
}

async function loadProductos(mode, proveedorId, regionId, tipoId, resultsEl) {
    if (!proveedorId || !regionId || !tipoId) {
        resultsEl.innerHTML = '<div class="info-box">Complete proveedor, región compatible y categoría para ver los productos.</div>';
        return;
    }

    resultsEl.innerHTML = '<div class="info-box">Cargando productos…</div>';

    const params = new URLSearchParams({
        proveedor_id: proveedorId,
        region_id: regionId,
        tipo_id: tipoId,
    });
    if (mode === 'carrito') {
        params.set('solo_disponibles', '1');
    }

    const response = await fetch(`${window.Cloud9.productosUrl}?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    const payload = await response.json();
    const productos = payload.productos || [];

    if (mode === 'carrito') {
        renderCartProducts(resultsEl, productos);
    } else {
        renderCatalogProducts(resultsEl, productos);
    }
}

function initAjaxFilters(root) {
    const mode = root.dataset.ajaxFilters;
    const proveedor = root.querySelector('[name="proveedor_id"]');
    const region = root.querySelector('[name="region_id"]');
    const tipo = root.querySelector('[name="tipo_id"]');
    const resultsEl = document.querySelector(root.dataset.resultsTarget || '#filtros-resultados');

    if (!proveedor || !region || !tipo || !resultsEl) {
        return;
    }

    // Evitar envío accidental del formulario de filtros.
    root.addEventListener('submit', (event) => event.preventDefault());

    const soloDisponibles = mode === 'carrito';

    proveedor.addEventListener('change', async () => {
        tipo.value = '';
        fillSelect(tipo, {}, 'Seleccione categoría');
        tipo.disabled = true;
        await loadRegiones(proveedor, region);
        resultsEl.innerHTML = '<div class="info-box">Complete proveedor, región compatible y categoría para ver los productos.</div>';
    });

    region.addEventListener('change', async () => {
        tipo.value = '';
        await loadTipos(proveedor.value, region.value, tipo, soloDisponibles);
        resultsEl.innerHTML = '<div class="info-box">Complete proveedor, región compatible y categoría para ver los productos.</div>';
    });

    tipo.addEventListener('change', async () => {
        await loadProductos(mode, proveedor.value, region.value, tipo.value, resultsEl);
    });
}

function initFormDependentFilters(form) {
    const proveedor = form.querySelector('[name="proveedor_id"]');
    const region = form.querySelector('[name="region_id"]');
    const tipo = form.querySelector('[name="tipo_id"]');

    if (!proveedor || !region) {
        return;
    }

    // En alta/edición: todos los tipos siguen disponibles; solo se actualizan regiones.
    // No hay auto-submit ni recarga.
    proveedor.addEventListener('change', async () => {
        const previousTipo = tipo?.value || null;
        await loadRegiones(proveedor, region);
        if (tipo) {
            // Mantener la categoría elegida si sigue siendo válida (todas lo son).
            if (previousTipo) {
                tipo.value = previousTipo;
            }
        }
    });
}

document.querySelectorAll('[data-ajax-filters]').forEach(initAjaxFilters);
document.querySelectorAll('form[data-dependent-filters]:not([data-ajax-filters])').forEach(initFormDependentFilters);

window.Cloud9UI = { openModal, closeModal, loadRegiones };
