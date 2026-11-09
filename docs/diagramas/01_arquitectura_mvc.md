# Diagrama 1 — Arquitectura MVC de Cloud 9 Systems

Fuente: código real (`routes/web.php`, controladores, Form Requests, modelos, `CarritoService`, vistas Blade y `resources/js/app.js`).

## Descripción

La aplicación sigue el patrón **MVC de Laravel**:

1. El **navegador** envía peticiones HTTP.
2. **`routes/web.php`** despacha a un controlador.
3. Cuando hay alta/edición, un **Form Request** valida antes de entrar al método del controlador.
4. El controlador usa **modelos Eloquent**, opcionalmente **`CarritoService`** (sesión) y/o **`Catalogo`** (constantes, no tabla).
5. Los datos persisten en la **base de datos** (excepto el carrito, que vive en sesión).
6. La respuesta es una **vista Blade**, un **redirect** o **JSON** (API de filtros / detalles modales).

No todas las peticiones pasan por Form Request ni por `CarritoService`. El filtrado del catálogo en pantalla usa **JavaScript** y endpoints `/api/*`.

## Diagrama Mermaid

```mermaid
flowchart TB
  subgraph Cliente["Cliente"]
    U["Usuario"]
    NAV["Navegador"]
    JS["JavaScript<br/>resources/js/app.js"]
  end

  subgraph HTTP["Capa HTTP Laravel"]
    R["routes/web.php"]
    FR["Form Requests<br/>Store/Update Producto<br/>Store/Update Proyecto<br/>Store Presupuesto<br/>Store Arquitectura"]
    CTRL["Controladores<br/>HomeController<br/>ProductoController<br/>ProyectoController<br/>CarritoController<br/>PresupuestoController<br/>ArquitecturaController<br/>CatalogoApiController"]
  end

  subgraph App["Dominio de aplicación"]
    CS["CarritoService<br/>sesión: carrito"]
    CAT["App\\Support\\Catalogo<br/>proveedor / región / tipo<br/>SIN tabla BD"]
    MOD["Modelos Eloquent<br/>Producto · Proyecto<br/>ProyectoRequisito<br/>Arquitectura · Presupuesto"]
  end

  subgraph Persistencia["Persistencia"]
    DB[("BD MySQL/SQLite<br/>tablas de dominio")]
    SES[("Sesión PHP<br/>clave carrito")]
  end

  subgraph UI["Presentación"]
    BLADE["Vistas Blade<br/>resources/views"]
  end

  U --> NAV
  NAV -->|"GET / POST / PUT / DELETE"| R
  R --> CTRL
  CTRL -.->|"altas y ediciones"| FR
  FR -->|"validated()"| CTRL
  CTRL --> CS
  CTRL --> MOD
  CTRL --> CAT
  CS <--> SES
  MOD <--> DB
  CTRL -->|"view / redirect"| BLADE
  BLADE -->|"HTML"| NAV
  NAV --> U

  NAV --> JS
  JS -->|"GET JSON /api/regiones|tipos|productos"| R
  R --> CTRL
  CTRL -->|"JSON"| JS
  JS -->|"actualiza DOM"| NAV
```

## Recorridos reales (no todos iguales)

| Caso | Ruta (ejemplo) | Form Request | CarritoService | Persistencia | Respuesta |
|------|----------------|--------------|----------------|--------------|-----------|
| Índice | `GET /` → `HomeController` | No | No | No | Blade `home` |
| Alta producto | `POST /catalogo` → `ProductoController@store` | `StoreProductoRequest` | No | Tabla `productos` | Redirect + flash |
| Filtros catálogo (UI) | `GET /api/productos` → `CatalogoApiController` | Validación `Request` | No | Lectura `productos` | JSON → JS |
| Añadir carrito | `POST /carrito/anadir` | Validación inline | Sí | Sesión + lectura producto | Redirect back |
| Crear presupuesto | `POST /presupuestos` | `StorePresupuestoRequest` | Sí (validar + limpiar) | `presupuestos` + pivote | Redirect listado |
| Listar presupuestos | `GET /presupuestos` | No | No | Eloquent `with('productos')` | Blade |

## Flujo canónico de una petición con validación

```text
Usuario → Ruta → Form Request → Controlador → Modelo/Servicio → BD/Sesión → Controlador → Vista/Redirect → Usuario
```

## Notas

- `Catalogo` no es un modelo Eloquent ni una tabla: solo constantes y helpers.
- El carrito **no** tiene tabla en migraciones de dominio.
- Los detalles (producto, proyecto, arquitectura, presupuesto) se exponen como **JSON** desde el controlador y el modal los renderiza en el cliente.
