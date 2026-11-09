<?php

namespace Tests\Feature;

use App\Models\Presupuesto;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesCloud9Data;
use Tests\TestCase;

/**
 * T09 — El importe histórico del presupuesto no cambia al modificar el precio del catálogo.
 */
class T09PresupuestoHistoricoInmutableTest extends TestCase
{
    use PreparesCloud9Data;
    use RefreshDatabase;

    public function test_importe_presupuesto_no_cambia_si_cambia_precio_catalogo(): void
    {
        $precios = [10, 20, 30, 40, 50, 60];
        $importeOriginal = array_sum($precios);
        $productos = $this->crearSeisProductosCarrito($precios);
        $this->llenarCarrito($productos);

        $this->post(route('presupuestos.store'), [
            'nombre' => 'Presupuesto T09',
            'tipo_pago' => 'Pago íntegro',
        ])->assertRedirect(route('presupuestos.index'));

        $presupuesto = Presupuesto::query()->where('nombre', 'Presupuesto T09')->firstOrFail();
        $this->assertSame($importeOriginal, $presupuesto->importe);

        // Simula cambio de catálogo a nivel de datos (la UI bloquearía productos asociados).
        $productoCatalogo = $productos->first();
        Producto::query()->whereKey($productoCatalogo->id)->update(['precio' => 9999]);

        $presupuesto->refresh();
        $this->assertSame($importeOriginal, $presupuesto->importe);
        $this->assertNotSame(
            $importeOriginal,
            (int) $presupuesto->productos()->sum('productos.precio')
        );
    }
}
