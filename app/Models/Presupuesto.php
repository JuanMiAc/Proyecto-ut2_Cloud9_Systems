<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Presupuesto extends Model
{
    protected $table = 'presupuestos';

    protected $fillable = [
        'nombre',
        'importe',
        'tipo_pago',
    ];

    protected function casts(): array
    {
        return [
            'importe' => 'integer',
        ];
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'presupuesto_producto');
    }
}
