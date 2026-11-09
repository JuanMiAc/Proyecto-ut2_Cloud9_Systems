<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arquitectura_producto', function (Blueprint $table) {
            $table->foreignId('arquitectura_id')->constrained('arquitecturas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->primary(['arquitectura_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arquitectura_producto');
    }
};
