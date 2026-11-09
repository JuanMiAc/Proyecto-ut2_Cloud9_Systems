<?php

namespace Tests\Feature;

use App\Models\Presupuesto;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesCloud9Data;
use Tests\TestCase;

/**
 * T02 — Segunda modalidad de pago (Fraccionado) y límite de precio entero/no negativo.
 * No inventa fórmulas de fraccionamiento: el importe sigue siendo la suma exacta.
 */
class T02PresupuestoFraccionadoOLimitePrecioTest extends TestCase
{
    use PreparesCloud9Data;
    use RefreshDatabase;

    public function test_presupuesto_fraccionado_usa_misma_suma_exacta(): void
    {
        $precios = [11, 22, 33, 44, 55, 66];
        $sumaEsperada = array_sum($precios);
        $productos = $this->crearSeisProductosCarrito($precios);
        $this->llenarCarrito($productos);

        $response = $this->post(route('presupuestos.store'), [
            'nombre' => 'Presupuesto T02 Fraccionado',
            'tipo_pago' => 'Fraccionado',
        ]);

        $response->assertRedirect(route('presupuestos.index'));

        $presupuesto = Presupuesto::query()->where('nombre', 'Presupuesto T02 Fraccionado')->first();
        $this->assertNotNull($presupuesto);
        $this->assertSame('Fraccionado', $presupuesto->tipo_pago);
        $this->assertSame($sumaEsperada, $presupuesto->importe);
    }

    public function test_precio_negativo_es_rechazado_por_validacion(): void
    {
        $response = $this->from(route('productos.create'))
            ->post(route('productos.store'), [
                'proveedor_id' => 1,
                'region_id' => 1,
                'tipo_id' => 1,
                'nombre' => 'Producto precio negativo T02',
                'descripcion' => 'Limite min 0',
                'precio' => -5,
                'disponible' => '1',
            ]);

        $response->assertRedirect(route('productos.create'));
        $response->assertSessionHasErrors('precio');
        $this->assertSame(0, Producto::query()->count());
    }
}
