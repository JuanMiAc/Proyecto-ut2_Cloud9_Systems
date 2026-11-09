<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('proveedor_id');
            $table->unsignedTinyInteger('region_id');
            $table->unsignedTinyInteger('tipo_id');
            $table->string('nombre', 150);
            $table->string('descripcion', 30);
            $table->integer('precio');
            $table->boolean('disponible')->default(true);
            $table->boolean('protegido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
