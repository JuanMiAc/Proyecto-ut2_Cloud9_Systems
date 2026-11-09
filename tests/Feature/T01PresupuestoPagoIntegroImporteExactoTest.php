<?php

namespace Tests\Feature;

use App\Models\Presupuesto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesCloud9Data;
use Tests\TestCase;

/**
 * T01 — Cálculo exacto del importe de un presupuesto con pago íntegro.
 */
class T01PresupuestoPagoIntegroImporteExactoTest extends TestCase
{
    use PreparesCloud9Data;
    use RefreshDatabase;

    public function test_importe_presupuesto_pago_integro_es_suma_exacta_de_precios(): void
    {
        $precios = [10, 20, 30, 40, 50, 60];
        $sumaEsperada = array_sum($precios);
        $productos = $this->crearSeisProductosCarrito($precios);
        $this->llenarCarrito($productos);

        $response = $this->post(route('presupuestos.store'), [
            'nombre' => 'Presupuesto T01',
            'tipo_pago' => 'Pago íntegro',
        ]);

        $response->assertRedirect(route('presupuestos.index'));
        $response->assertSessionHas('success');

        $presupuesto = Presupuesto::query()->where('nombre', 'Presupuesto T01')->first();
        $this->assertNotNull($presupuesto);
        $this->assertSame('Pago íntegro', $presupuesto->tipo_pago);
        $this->assertSame($sumaEsperada, $presupuesto->importe);
        $this->assertSame(6, $presupuesto->productos()->count());
    }
}
