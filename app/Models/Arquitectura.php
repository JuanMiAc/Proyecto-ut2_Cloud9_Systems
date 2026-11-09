<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Arquitectura extends Model
{
    protected $table = 'arquitecturas';

    protected $fillable = [
        'proyecto_id',
        'nombre',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'arquitectura_producto');
    }

    public function getImporteTotalAttribute(): int
    {
        if ($this->relationLoaded('productos')) {
            return (int) $this->productos->sum('precio');
        }

        return (int) $this->productos()->sum('productos.precio');
    }
}
