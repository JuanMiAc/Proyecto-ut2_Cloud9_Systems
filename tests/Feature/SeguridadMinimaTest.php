<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesCloud9Data;
use Tests\TestCase;

/**
 * Comprobaciones de seguridad mínima compatibles con la aplicación real.
 */
class SeguridadMinimaTest extends TestCase
{
    use PreparesCloud9Data;
    use RefreshDatabase;

    public function test_formularios_y_layout_exponen_proteccion_csrf(): void
    {
        // En PHPUnit, PreventRequestForgery omite la validación (runningUnitTests).
        // Se comprueba el mecanismo real: @csrf en formularios POST y meta/token JS del layout.
        $this->assertTrue(
            class_exists(PreventRequestForgery::class),
            'El middleware CSRF de Laravel debe estar disponible.'
        );

        $alta = $this->get(route('productos.create'));
        $alta->assertOk();
        $alta->assertSee('name="_token"', false);
        $alta->assertSee('name="csrf-token"', false);

        $carrito = $this->get(route('carrito.index'));
        $carrito->assertOk();
        $carrito->assertSee('name="csrf-token"', false);
        $carrito->assertSee('csrfToken:', false);
    }

    public function test_validacion_servidor_rechaza_payload_incompleto(): void
    {
        $response = $this->from(route('productos.create'))
            ->post(route('productos.store'), [
                'proveedor_id' => 1,
                // faltan region_id, tipo_id, nombre, etc.
            ]);

        $response->assertRedirect(route('productos.create'));
        $response->assertSessionHasErrors(['region_id', 'tipo_id', 'nombre', 'descripcion', 'precio']);
        $this->assertSame(0, Producto::query()->count());
    }

    public function test_alta_producto_no_permite_marcar_protegido_por_asignacion_masiva_http(): void
    {
        $response = $this->post(route('productos.store'), [
            'proveedor_id' => 1,
            'region_id' => 1,
            'tipo_id' => 1,
            'nombre' => 'Intento protegido HTTP',
            'descripcion' => 'Mass assignment',
            'precio' => 10,
            'disponible' => '1',
            'protegido' => '1',
        ]);

        $response->assertRedirect();

        $producto = Producto::query()->where('nombre', 'Intento protegido HTTP')->first();
        $this->assertNotNull($producto);
        // StoreProductoRequest no valida protegido; el controlador fuerza protegido=false.
        $this->assertFalse($producto->protegido);
    }

    public function test_integridad_restrict_impide_borrar_producto_asociado_a_presupuesto(): void
    {
        $precios = [1, 1, 1, 1, 1, 1];
        $productos = $this->crearSeisProductosCarrito($precios);
        $this->llenarCarrito($productos);

        $this->post(route('presupuestos.store'), [
            'nombre' => 'Presupuesto seguridad',
            'tipo_pago' => 'Pago íntegro',
        ])->assertRedirect(route('presupuestos.index'));

        $producto = $productos->first();
        // Aunque no esté protegido, está asociado: la app bloquea el borrado.
        $producto->update(['protegido' => false]);

        $response = $this->from(route('productos.index'))
            ->delete(route('productos.destroy', $producto));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_vistas_no_usan_escape_raw_blade(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'))
        );

        $archivosConRaw = [];
        foreach ($iterator as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $contenido = file_get_contents($file->getPathname());
            if ($contenido !== false && str_contains($contenido, '{!!')) {
                $archivosConRaw[] = $file->getPathname();
            }
        }

        $this->assertSame([], $archivosConRaw, 'No deben usarse echo raw {!! !!} en vistas Blade.');
    }
}
