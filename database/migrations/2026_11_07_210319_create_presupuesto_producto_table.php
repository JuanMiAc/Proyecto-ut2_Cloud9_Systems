<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuesto_producto', function (Blueprint $table) {
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->primary(['presupuesto_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuesto_producto');
    }
};
