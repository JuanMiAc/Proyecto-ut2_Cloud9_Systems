<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\ProyectoRequisito;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T07 — Operación CRUD / relación Eloquent con datos reales.
 */
class T07CrudORelacionEloquentTest extends TestCase
{
    use RefreshDatabase;

    public function test_alta_proyecto_crea_requisitos_relacionados(): void
    {
        $response = $this->post(route('proyectos.store'), [
            'nombre' => 'Proyecto T07',
            'descripcion' => 'Proyecto para probar relación hasMany requisitos',
            'presupuesto' => 1000,
            'estado' => '1',
            'requisitos' => [
                'Requisito uno T07',
                'Requisito dos T07',
            ],
        ]);

        $response->assertRedirect(route('proyectos.index'));

        $proyecto = Proyecto::query()->where('nombre', 'Proyecto T07')->first();
        $this->assertNotNull($proyecto);
        $this->assertDatabaseHas('proyectos', [
            'id' => $proyecto->id,
            'presupuesto' => 1000,
        ]);

        $this->assertSame(2, $proyecto->requisitos()->count());
        $this->assertEqualsCanonicalizing(
            ['Requisito uno T07', 'Requisito dos T07'],
            $proyecto->requisitos()->pluck('requisito')->all()
        );
        $this->assertSame(2, ProyectoRequisito::query()->where('proyecto_id', $proyecto->id)->count());
    }
}
