# CLOUD 9 SYSTEMS
## PLAN MAESTRO — ESPECIFICACIONES FUNCIONALES Y TÉCNICAS

**Versión:** 3  
**Estado:** Documentación de referencia vigente. No autoriza la implementación hasta aprobación expresa del equipo.

### Objetivo de este documento
Establecer las especificaciones funcionales y técnicas acordadas para Cloud 9 Systems.

Este documento, junto con `VISTAS.pdf` y `TABLAS PRODUCTOS.pdf` (referencia visual e histórica del catálogo), constituye la documentación de referencia. La fuente de verdad de reglas de negocio es este Plan Maestro.

### Principios de interpretación
- Las decisiones más recientes acordadas en conversaciones prevalecen sobre PDF, `.cursorrules` desactualizado o versiones anteriores de este plan.
- Los PDF no autorizan recuperar funcionalidades descartadas.
- No se inventa ni se incorpora funcionalidad por iniciativa propia.
- Cambios meramente visuales pueden resolverse en desarrollo sin alterar la arquitectura.
- Cambios de datos, relaciones, reglas de negocio, flujos, validaciones o persistencia requieren aprobación previa.
- Documento de referencia del agente: `.cursorrules` debe mantenerse alineado con esta versión.

---

# 1. OBJETIVO GENERAL

Cloud 9 Systems es una aplicación web Laravel para gestionar productos cloud, proyectos, arquitecturas y presupuestos.

Permitirá:

- Consultar un catálogo de productos de distintos proveedores.
- Seleccionar productos en un carrito temporal (sesión).
- Crear presupuestos o arquitecturas a partir de esa selección.
- Registrar proyectos y sus requisitos.
- Consultar listados y detalles (detalle en ventanas emergentes).

**Sin autenticación ni usuarios.** La aplicación no gestionará login, roles ni cuentas.

---

# 2. NAVEGACIÓN GENERAL

## 2.1. Menú superior
Todas las páginas comparten un layout Blade con menú superior:

1. Índice  
2. Catálogo  
3. Carrito  
4. Proyecto  
5. Arquitectura  
6. Presupuesto  

Reglas:

- **Arquitectura** y **Presupuesto** abren directamente sus listados.
- La opción **activa** está **resaltada visualmente** y **no es pulsable** (no navega a la misma sección).
- No hay buscador complejo de productos; se usan selectores en secuencia obligatoria (sección 6).

## 2.2. Confirmación al abandonar
La confirmación debe aparecer **siempre** que el usuario intente abandonar la página actual, **independientemente** de que haya datos introducidos o no. **No** se establecen condiciones para decidir si se muestra el aviso: se muestra **siempre**.

Se aplica a **cualquier** forma de navegación que abandone la página, entre otras:

- Pulsar una opción del menú principal.
- Pulsar un enlace **Volver**.
- Salir desde las vistas **Crear** o **Editar** hacia sus respectivos listados.
- Pulsar cualquier enlace o acción que abandone la página actual.

El aviso debe preguntar si está seguro de salir e informar de que se perderá la información no guardada.

- Si el usuario **cancela**, permanece en la página actual y **conserva** los datos.
- Si el usuario **confirma**, abandona la página y se **descarta** la información temporal, **incluido el carrito** cuando corresponda.

## 2.3. Formularios
- Protección `@csrf` obligatoria.
- Botón o enlace visible de retorno / cancelación.
- Validación definitiva en servidor (Form Requests); JavaScript solo como apoyo visual.

---

# 3. VISTAS DEFINITIVAS

| # | Vista | Función |
|---|--------|---------|
| 1 | Índice | Acceso a las secciones |
| 2 | Listar catálogo | Filtros en secuencia; acciones según reglas; detalle en modal |
| 3 | Crear producto | Formulario propio |
| 4 | Editar producto | Vista/formulario propios; estructura y campos equivalentes a crear |
| 5 | Carrito | Configurador en sesión |
| 6 | Listar proyectos | Acceso a editar (vista), detalle (modal), eliminar si procede |
| 7 | Crear proyecto | Formulario propio |
| 8 | Editar proyecto | Vista/formulario propios; mismos campos que crear, precargados |
| 9 | Listar arquitecturas | Solo listado + detalle en modal |
| 10 | Listar presupuestos | Solo listado + detalle en modal |

### Criterios de UI acordados
- **Creación y edición** de productos y de proyectos: siempre mediante **sus vistas y formularios** dedicados (no edición en modal).
- **Consulta de detalle** de productos, proyectos, arquitecturas y presupuestos: **ventanas emergentes** con botón **Cerrar**, volviendo al listado y conservando su contexto.
- No existen vistas de edición ni eliminación de arquitecturas o presupuestos.

---

# 4. BASE DE DATOS

Siete tablas. No añadir tablas sin aprobación.

## 4.1. productos

| Campo | Tipo | Restricciones |
|-------|------|----------------|
| id | BIGINT UNSIGNED | PK, autoincremental |
| proveedor_id | TINYINT UNSIGNED | obligatorio |
| region_id | TINYINT UNSIGNED | obligatorio |
| tipo_id | TINYINT UNSIGNED | obligatorio |
| nombre | VARCHAR(150) | obligatorio |
| descripcion | VARCHAR(30) | obligatorio |
| precio | INTEGER | obligatorio |
| disponible | BOOLEAN | obligatorio, default TRUE |
| protegido | BOOLEAN | obligatorio, default FALSE |
| timestamps | Laravel | `created_at`, `updated_at` |

### Reglas de productos
- **38 productos iniciales** (seed según `TABLAS PRODUCTOS.pdf` / INSERT acordado), con **`protegido = TRUE`**.
- Todos los importes del sistema son **números enteros** (sin decimales).
- Seed (38): precios enteros obtenidos a partir de los originales **eliminando la parte decimal sin redondear** (p. ej. `1,55` → `1`; `0,02000` → `0`).
- Productos nuevos: `protegido = FALSE`.
- Productos nuevos: precio **entero** (validación Laravel). No se admiten decimales en formularios.
- **Protegidos:** no se editan ni se eliminan.
- **Cualquier producto asociado** a un presupuesto o a una arquitectura: **no se edita ni se elimina** (ni precio ni resto de campos).
- Productos nuevos **sin** asociaciones: pueden editarse y eliminarse.
- `disponible = FALSE`: no seleccionables en el carrito.
- En listados, **Editar** / **Eliminar** solo si las reglas lo permiten; el detalle se abre en modal.

### Consecuencia sobre importes
Al no poder modificarse el precio de un producto ya asociado a presupuestos o arquitecturas, **no existe** un flujo de recálculo/actualización de importes de presupuestos ni de arquitecturas por cambio de precio. Esa mecánica queda **descartada**.

## 4.2. proyectos

| Campo | Tipo | Restricciones |
|-------|------|----------------|
| id | BIGINT UNSIGNED | PK, autoincremental |
| nombre | VARCHAR(150) | obligatorio |
| descripcion | TEXT | obligatorio |
| presupuesto | INTEGER | obligatorio |
| estado | BOOLEAN | obligatorio, default TRUE |
| timestamps | Laravel | |

- Varios requisitos y varias arquitecturas posibles.
- Con arquitecturas asociadas: **no eliminar**; no mostrar botón Eliminar.
- Sin arquitecturas: al eliminar el proyecto se eliminan sus **requisitos**; **nunca** se eliminan arquitecturas.

## 4.3. proyecto_requisitos

| Campo | Tipo | Restricciones |
|-------|------|----------------|
| id | BIGINT UNSIGNED | PK, autoincremental |
| proyecto_id | BIGINT UNSIGNED | FK, obligatorio |
| requisito | VARCHAR(255) | obligatorio |
| timestamps | Laravel | |

## 4.4. arquitecturas

| Campo | Tipo | Restricciones |
|-------|------|----------------|
| id | BIGINT UNSIGNED | PK, autoincremental |
| proyecto_id | BIGINT UNSIGNED | FK, **nullable** |
| nombre | VARCHAR(20) | obligatorio |
| estado | BOOLEAN | obligatorio, default TRUE |
| timestamps | Laravel | |

- Puede existir **sin** proyecto o vinculada a **un** proyecto.
- Se crea desde el carrito con los **seis** productos.
- En la creación, la asociación a proyecto es **opcional**.
- Listado + detalle en modal; **no editar ni eliminar**.
- El total mostrado es la **suma de los precios** de sus productos asociados (precios que no cambiarán tras la asociación).
- No se guarda precio histórico en el pivote.

## 4.5. arquitectura_producto

PK compuesta: `arquitectura_id` + `producto_id` (FKs). Sin precio histórico.

## 4.6. presupuestos

| Campo | Tipo | Restricciones |
|-------|------|----------------|
| id | BIGINT UNSIGNED | PK, autoincremental |
| nombre | VARCHAR(150) | obligatorio |
| importe | INTEGER | obligatorio |
| tipo_pago | VARCHAR(20) | obligatorio |
| timestamps | Laravel | |

Valores de `tipo_pago`:

- `Pago íntegro`
- `Fraccionado`

- Se crea desde el carrito; **sin** relación directa con proyectos.
- Alta: **nombre**, **tipo de pago** e **importe** calculado como **suma entera** de los precios enteros de los **seis** productos del carrito (sin redondeos ni operaciones con decimales).
- Listado + detalle en modal; **no editar ni eliminar**.
- El `importe` queda fijado en la creación; **no** se actualiza después por cambios de precio (los productos asociados no pueden cambiar de precio).

## 4.7. presupuesto_producto

PK compuesta: `presupuesto_id` + `producto_id` (FKs). Sin precio histórico.

### Claves foráneas
Proteger relaciones con FKs. Borrado de proyecto: cascade de requisitos; no borrar arquitecturas; no permitir borrar proyecto si tiene arquitecturas (regla de aplicación + UX).

---

# 5. IDENTIFICADORES FIJOS (SIN TABLAS)

Gestionados en Laravel (arrays, constantes, enums o config). Estables en toda la app.

### Proveedores
- 1: AWS  
- 2: Azure  

### Regiones
- 1: Irlanda  
- 2: Frankfurt  
- 3: Londres  
- 4: West Europe  
- 5: North Europe  

AWS → regiones 1–3. Azure → regiones 4–5.

### Tipos / categorías (`tipo_id`)
- 1: Computación  
- 2: Almacenamiento  
- 3: Bases de datos  
- 4: Redes  
- 5: Seguridad  
- 6: Monitorización  

---

# 6. CATÁLOGO Y PRODUCTOS

## 6.1. Secuencia obligatoria (filtros dependientes)
Orden fijo y completo:

1. Seleccionar **proveedor**.  
2. Mostrar **únicamente** las regiones compatibles con ese proveedor (y permitir seleccionar región).  
3. Seleccionar **categoría** (`tipo_id`).  
4. Mostrar los **productos** correspondientes a las tres selecciones anteriores.  

Esta secuencia se aplica en: **Catálogo**, **Carrito**, **Crear producto** y **Editar producto**.

## 6.2. Acciones del listado
Según reglas: Editar, Eliminar; detalle en ventana emergente.

## 6.3. Vistas crear / editar producto
- Interfaces distintas, estructura y campos equivalentes.
- En **Editar producto**, encima de los filtros: enlace/opción **Crear producto**.

## 6.4. Precios e importes (enteros)
- `productos.precio`, `presupuestos.importe` y `proyectos.presupuesto` son **INTEGER**.
- Todos los importes son números enteros. No se admiten decimales en ningún formulario.
- Laravel valida los importes como enteros. La interfaz muestra exclusivamente números enteros.
- Seed (38): convertir precios originales **truncando** (eliminar parte decimal **sin redondear**).
- Productos nuevos: solo precios enteros.
- Presupuestos: suma de los precios enteros de los seis productos seleccionados.
- Sin redondeos ni operaciones con decimales. Sin recálculo posterior de importes de presupuestos/arquitecturas por cambio de precio de asociados.

---

# 7. CARRITO (SESIÓN)

Temporal en sesión Laravel. Sin tabla de carritos ni carritos abandonados persistidos.

## 7.1. Seis categorías obligatorias
Una por `tipo_id` (Computación … Monitorización).

## 7.2. Reglas
- Exactamente **un** producto por categoría.
- Sustitución si se elige otro de la misma categoría.
- Se puede quitar; categoría vacía impide finalizar.
- Solo productos `disponible = TRUE`.
- Filtros dependientes según §6.1: proveedor → regiones compatibles → categoría → productos.
- Validación servidor: seis productos válidos, seis tipos distintos.
- Sin cantidades.

## 7.3. Finalización
Con las seis categorías completas:

### Crear presupuesto
Solicitar:

- Nombre  
- Tipo de pago (`Pago íntegro` | `Fraccionado`)  
- Importe = suma entera de los precios enteros de los seis productos (calculado por la aplicación; no editable como valor libre arbitrario)

Guardar presupuesto + filas en `presupuesto_producto` → limpiar carrito.

### Crear arquitectura
Solicitar:

- Nombre (máx. 20 caracteres)  
- Asociación a proyecto: **opcional** (`proyecto_id` o independiente)

Guardar arquitectura + filas en `arquitectura_producto` → limpiar carrito.

---

# 8. VENTANAS EMERGENTES DE DETALLE

Para: productos, proyectos, arquitecturas, presupuestos.

- Botón **Cerrar**.
- Al cerrar: volver al listado conservando contexto (filtros, etc.).
- Arquitecturas y presupuestos: **solo** consulta de detalle desde el listado.

---

# 9. PROYECTOS — FLUJOS

- Listar y crear: vistas distintas.
- Crear y editar: vistas/formularios dedicados (misma estructura; vacío vs precargado).
- Desde el listado: ir a **editar** (vista 8) y **detalle** (modal).
- Eliminación según sección 4.2.

---

# 10. ARQUITECTURAS Y PRESUPUESTOS

Permanentes e **inmutables** tras la creación (datos y asociaciones).

- Origen: carrito.
- Menú → listados.
- Solo detalle en modal.
- Sin edición, sin borrado.
- Sin actualización posterior de importes por cambio de precio de productos.

---

# 11. IMPORTES (REGLA VIGENTE)

| Concepto | Comportamiento |
|----------|----------------|
| Presupuesto.`importe` | Calculado y persistido **en la creación**; no se recalcula después |
| Total de arquitectura | Suma de precios de productos asociados al **mostrar**; esos precios no cambian tras asociarse |
| Pivotes | Solo IDs de producto; sin precio histórico unitario |
| Cambio de precio de producto | Solo posible si el producto **no** está asociado (y no está protegido); no dispara actualización de presupuestos/arquitecturas |

Queda **derogada** cualquier regla anterior de actualización transaccional de importes al modificar precios de productos asociados.

---

# 12. REGLAS GENERALES

- Sin autenticación ni usuarios.
- Presupuestos y arquitecturas: no editar ni eliminar.
- Productos protegidos: no editar ni eliminar.
- Productos asociados a presupuesto o arquitectura: no editar ni eliminar.
- Validaciones importantes en servidor.
- FKs donde corresponda.
- Sin tablas ni funcionalidades no acordadas.
- Confirmación de salida **siempre** ante cualquier abandono de página; al confirmar, descarte de temporales, incluido el carrito cuando corresponda (§2.2).
- Menú: activa resaltada y no pulsable.
- Cada control de UI con función clara.

---

# 13. PDF Y ALCANCE VISUAL

Referencia: `VISTAS.pdf`, `TABLAS PRODUCTOS.pdf`.

Descartado respecto a esos PDF (entre otros):

- Carrito de solo tres categorías.
- Edición/borrado de arquitecturas o presupuestos.
- CPU/RAM/disco en alta de producto.
- Arrays finales erróneos de regiones/tipos del PDF de productos.
- Persistencia MySQL del carrito.
- Recálculo de importes por cambio de precio de productos ya asociados.
- Autenticación / perfiles de usuario.

---

# 14. DECISIONES PENDIENTES

Tras la revisión de la versión 3, **no quedan decisiones de negocio abiertas** que bloqueen el diseño acordado.

Cualquier duda menor de presentación (textos, estilos, distribución) se resolverá en desarrollo **sin** cambiar estructura de datos ni reglas de este documento.

Si durante la implementación surgiera un conflicto real con este plan, se detendrá el trabajo y se consultará al equipo **antes** de decidir.

---

# 15. ORDEN DE IMPLEMENTACIÓN ORIENTATIVO

1. Migraciones (7 tablas) + seed 38 productos protegidos (precios enteros truncados desde el original).  
2. Layout + menú (6 opciones, activa no pulsable) + Índice.  
3. Catálogo: filtros, listado, crear, editar, eliminar según reglas, modal detalle.  
4. Proyectos: listar, crear, editar, modal detalle, borrado condicionado.  
5. Carrito en sesión (6 categorías + validación).  
6. Alta presupuesto (nombre, tipo_pago, importe) y arquitectura (nombre, proyecto opcional) + listados + modales.  
7. Confirmación de abandono siempre (cualquier salida de página) y vaciado de temporales al confirmar (incluido carrito cuando corresponda).  
8. Pruebas de protección, asociaciones e inmutabilidad.

**No implementar hasta aprobación expresa de esta versión 3.**
