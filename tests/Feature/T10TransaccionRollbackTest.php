<?php

namespace Tests\Feature;

use App\Models\Presupuesto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Support\PreparesCloud9Data;
use Tests\TestCase;

/**
 * T10 — Una transacción revierte los datos cuando se provoca un fallo.
 */
class T10TransaccionRollbackTest extends TestCase
{
    use PreparesCloud9Data;
    use RefreshDatabase;

    public function test_fallo_durante_alta_presupuesto_no_deja_datos_incompletos(): void
    {
        $productos = $this->crearSeisProductosCarrito([5, 5, 5, 5, 5, 5]);
        $this->llenarCarrito($productos);

        Presupuesto::created(function (): void {
            throw new RuntimeException('Fallo forzado en test T10');
        });

        // Laravel convierte la excepción en respuesta HTTP 500; la transacción debe hacer rollback.
        $response = $this->post(route('presupuestos.store'), [
            'nombre' => 'Presupuesto T10 incompleto',
            'tipo_pago' => 'Pago íntegro',
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('presupuestos', [
            'nombre' => 'Presupuesto T10 incompleto',
        ]);
        $this->assertSame(0, Presupuesto::query()->count());
        $this->assertDatabaseCount('presupuesto_producto', 0);
    }
}
