# Cloud 9 Systems (UT2)

Aplicación web académica en **Laravel** para gestionar un catálogo de productos cloud (AWS/Azure), un carrito de configuración por categorías, proyectos, arquitecturas y presupuestos.

Este repositorio corresponde a la entrega de código de la **UT2**. El diseño funcional de referencia está descrito en `docs/PLAN_MAESTRO.md` (versión 3).

## Objetivo

Permitir:

- Consultar y mantener el **catálogo** de productos (filtros por proveedor → región compatible → categoría).
- Configurar un **carrito** en sesión con exactamente un producto de cada una de las 6 categorías.
- Crear **presupuestos** (importe = suma entera de precios; tipos de pago «Pago íntegro» o «Fraccionado») y **arquitecturas** (opcionalmente asociadas a un proyecto).
- Gestionar **proyectos** con requisitos.
- Consultar listados y detalles (modales) de arquitecturas y presupuestos.

## Requisitos comprobados en este entorno

| Herramienta | Versión usada en desarrollo |
|-------------|-----------------------------|
| PHP | 8.5.4 (el proyecto declara `php: ^8.3` en `composer.json`) |
| Extensiones PHP recomendadas | `pdo_mysql` y/o `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath` |
| Composer | 2.9.5 |
| Laravel Framework | 13.40.0 (`laravel/framework` en `composer.lock`) |
| Node.js | 24.22.1 |
| npm | 11.19.1 |

Para ejecutar los tests con la configuración actual de `phpunit.xml` (SQLite en memoria) es necesario el módulo **`pdo_sqlite`** (por ejemplo el paquete del sistema `php8.5-sqlite3` si usas PHP 8.5).

## Instalación

### 1. Obtener el código

Descomprime el ZIP de entrega y entra en la carpeta del proyecto (la que contiene `artisan`):

```bash
cd cloud9-systems
```

### 2. Dependencias PHP

```bash
composer install
```

### 3. Dependencias frontend (opcional para arrancar)

La interfaz en producción de desarrollo carga los assets desde `public/css/app.css` y `public/js/app.js` (ya incluidos en la entrega).

Si necesitas **reconstruir** esos assets a partir de `resources/` con Vite:

```bash
npm install
npm run build
```

Nota: el layout actual usa `asset('css/app.css')` y `asset('js/app.js')`. Tras `npm run build`, Vite genera salida en `public/build/` (ignorada por `.gitignore`). Si regeneras assets, asegúrate de actualizar también `public/css` y `public/js` si ese es el flujo que uses en tu entorno, o adapta el despliegue a lo que realmente sirva la aplicación.

Para desarrollo con Vite en caliente (opcional):

```bash
npm run dev
```

En este proyecto el arranque habitual no depende de `npm run dev` si ya existen los ficheros en `public/css` y `public/js`.

### 4. Entorno

```bash
cp .env.example .env
php artisan key:generate
```

No copies ni entregues un fichero `.env` con secretos. Usa siempre `.env.example` como plantilla.

### 5. Base de datos

Por defecto, `.env.example` usa **SQLite**:

```env
DB_CONNECTION=sqlite
```

Crea el fichero de base de datos si aún no existe:

```bash
touch database/database.sqlite
```

También puedes usar **MySQL/MariaDB**. En `.env`, configura por ejemplo:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cloud9_systems
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

Crea la base de datos vacía en el servidor MySQL antes de migrar. **No uses credenciales de ejemplo de este README**: pon las de tu entorno local.

`.env.example` deja `SESSION_DRIVER=database`, `CACHE_STORE=database` y `QUEUE_CONNECTION=database`. Las migraciones por defecto de Laravel crean las tablas necesarias (`sessions`, `cache`, `jobs`, etc.).

### 6. Migraciones y seeders

```bash
php artisan migrate
php artisan db:seed
```

El seeder principal (`DatabaseSeeder`) llama a `ProductosSeeder`, que inserta el catálogo inicial de productos protegidos con precios enteros.

### 7. Arrancar la aplicación

```bash
php artisan serve
```

Abre en el navegador la URL indicada (por defecto `http://127.0.0.1:8000`).

## Tests automatizados

Con las dependencias instaladas y `pdo_sqlite` disponible:

```bash
php artisan test
```

O, de forma equivalente:

```bash
./vendor/bin/phpunit
```

Los tests de dominio usan `RefreshDatabase` y la base SQLite en memoria definida en `phpunit.xml`. **No modifican** la base de datos de desarrollo MySQL/SQLite del fichero `.env`.

## Funcionalidades disponibles (esta versión UT2)

- Menú: Índice, Catálogo, Carrito, Proyecto, Arquitectura, Presupuesto.
- Catálogo con filtros dependientes proveedor → región → categoría; alta, edición y borrado según reglas (`protegido`, asociaciones).
- Carrito en **sesión** (sin tabla de carritos): un producto por categoría; creación de presupuesto o arquitectura.
- Proyectos con requisitos; no se pueden eliminar si tienen arquitecturas.
- Arquitecturas y presupuestos: creación desde el carrito; listado y detalle; **sin** edición ni borrado.
- Importes enteros (sin decimales) en productos, proyectos y presupuestos.
- Confirmación de abandono de página en la interfaz (JavaScript).
- Tests Feature T01–T07, T09, T10 y comprobaciones de seguridad mínima en `tests/Feature/`.

## Limitaciones conocidas

- **Sin autenticación** de usuarios ni roles.
- **Sin subida de ficheros** (T08 del enunciado de pruebas no aplica).
- Los tipos de pago «Pago íntegro» y «Fraccionado» se almacenan como modalidad; **no** hay cálculo distinto de cuotas.
- El importe del **presupuesto** es histórico (columna `importe`); el total mostrado de una **arquitectura** se calcula como suma actual de precios del catálogo.
- Proveedores, regiones y categorías son identificadores fijos en código (`App\Support\Catalogo`), no tablas.
- No se recalculan presupuestos ya creados al cambiar precios del catálogo.
- No hay carrito persistente en MySQL.

## Documentación del proyecto

La carpeta `docs/` agrupa la documentación de referencia de la UT2:

| Documento | Contenido |
|-----------|-----------|
| `docs/PLAN_MAESTRO.md` | **Plan Maestro** (v3): reglas de negocio, modelo de datos, navegación y alcance acordado. Fuente de verdad funcional. |
| `docs/Cloud_9_Systems_Informe_Pruebas_Seguridad.pdf` | **Informe de pruebas y seguridad**: resultados de los tests automatizados y comprobaciones de seguridad mínima. |
| `docs/TABLAS PRODUCTOS.pdf` | **Documentación de tablas y productos**: referencia histórica del catálogo / productos. |
| `docs/VISTAS.pdf` | **Documentación de vistas**: referencia visual de pantallas (parte del material descartado o adaptado queda reflejada en el Plan Maestro). |

## Estructura relevante

```
app/            # Modelos, controladores, Form Requests, servicios
bootstrap/      # Arranque de Laravel
config/         # Configuración
database/       # Migraciones y seeders
docs/           # Plan Maestro, informe de pruebas y PDFs de referencia
public/         # Front controller y assets servidos (css/, js/)
resources/      # Vistas Blade y fuentes CSS/JS
routes/         # Rutas web
tests/          # PHPUnit (Feature / Unit / Support)
```

## Licencia

El esqueleto Laravel original se distribuye bajo la licencia MIT del framework. El código de dominio de Cloud 9 Systems forma parte de la entrega académica UT2.
