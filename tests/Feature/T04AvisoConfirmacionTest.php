<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T04 — Mecanismo real de aviso/confirmación existente en la interfaz.
 */
class T04AvisoConfirmacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_incluye_enlaces_con_confirmacion_de_abandono(): void
    {
        $response = $this->get(route('productos.create'));

        $response->assertOk();
        $response->assertSee('confirm-nav', false);
        $response->assertSee('data-confirm-nav', false);
        // Mensaje de confirmación definido en resources/js/app.js (CONFIRM_MSG).
        $response->assertSee('js/app', false);
    }

    public function test_listado_proyectos_incluye_confirmacion_antes_de_eliminar(): void
    {
        $this->post(route('proyectos.store'), [
            'nombre' => 'Proyecto T04',
            'descripcion' => 'Para comprobar confirm en borrado',
            'presupuesto' => 100,
            'estado' => '1',
        ])->assertRedirect(route('proyectos.index'));

        $response = $this->get(route('proyectos.index'));

        $response->assertOk();
        $response->assertSee('onsubmit="return confirm(', false);
        $response->assertSee('¿Eliminar este proyecto y sus requisitos?', false);
    }
}
