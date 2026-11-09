# Diagrama 2 — Modelo entidad-relación (dominio)

Fuente: migraciones `database/migrations/2026_11_07_*` y modelos en `app/Models/`.

## Alcance

Entidades de dominio de Cloud 9 Systems. **No** se incluyen `users`, `cache`, `jobs` ni tablas de sesión.

**No existen** entidades/tablas `proveedores`, `regiones` ni `tipos`: en `productos` son atributos enteros interpretados por `App\Support\Catalogo` (relación **lógica**, sin FK).

## Diagrama Mermaid (ER)

```mermaid
erDiagram
  PROYECTOS ||--o{ PROYECTO_REQUISITOS : "1:N"
  PROYECTOS ||--o{ ARQUITECTURAS : "1:N opcional"
  ARQUITECTURAS }o--o{ PRODUCTOS : "N:M"
  PRESUPUESTOS }o--o{ PRODUCTOS : "N:M"

  PRODUCTOS {
    bigint id PK
    tinyint proveedor_id "logico Catalogo"
    tinyint region_id "logico Catalogo"
    tinyint tipo_id "logico Catalogo"
    varchar nombre
    varchar descripcion
    int precio
    boolean disponible
    boolean protegido
  }

  PROYECTOS {
    bigint id PK
    varchar nombre
    text descripcion
    int presupuesto
    boolean estado
  }

  PROYECTO_REQUISITOS {
    bigint id PK
    bigint proyecto_id FK
    varchar requisito
  }

  ARQUITECTURAS {
    bigint id PK
    bigint proyecto_id FK "nullable"
    varchar nombre
    boolean estado
  }

  ARQUITECTURA_PRODUCTO {
    bigint arquitectura_id PK_FK
    bigint producto_id PK_FK
  }

  PRESUPUESTOS {
    bigint id PK
    varchar nombre
    int importe
    varchar tipo_pago
  }

  PRESUPUESTO_PRODUCTO {
    bigint presupuesto_id PK_FK
    bigint producto_id PK_FK
  }
```

## Cardinalidades y tipología

| Relación | Cardinalidad | Tipo de entidad intermedia | Eloquent | FK física |
|----------|--------------|----------------------------|----------|-----------|
| Proyecto → requisitos | 1 : N | — (entidad débil con PK propia) | `hasMany` / `belongsTo` | Sí |
| Proyecto → arquitecturas | 1 : 0..N | — | `hasMany` / `belongsTo` | Sí, **nullable** |
| Arquitectura ↔ productos | N : M | `arquitectura_producto` | `belongsToMany` | Sí |
| Presupuesto ↔ productos | N : M | `presupuesto_producto` | `belongsToMany` | Sí |
| Producto → proveedor/región/tipo | N : 1 lógico | No hay entidad | Accesorios `Catalogo` | **No** |

## Entidades principales vs intermedias

**Principales:** `productos`, `proyectos`, `proyecto_requisitos`, `arquitecturas`, `presupuestos`.

**Intermedias (solo pivote, PK compuesta):** `arquitectura_producto`, `presupuesto_producto`.

## Relaciones físicas vs lógicas

- **Físicas:** todas las FK declaradas en migraciones (ver diagrama 03).
- **Lógicas sin FK:** `productos.proveedor_id`, `region_id`, `tipo_id` ↔ constantes de `Catalogo`.
- **Sin entidad de carrito:** el carrito es estado de sesión (`CarritoService`), no aparece en el ER.

## Coherencia con Eloquent

- `Proyecto::requisitos()`, `Proyecto::arquitecturas()`
- `ProyectoRequisito::proyecto()`
- `Arquitectura::proyecto()`, `Arquitectura::productos()`
- `Presupuesto::productos()`
- `Producto::arquitecturas()`, `Producto::presupuestos()`
