<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'presupuesto',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'presupuesto' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function requisitos(): HasMany
    {
        return $this->hasMany(ProyectoRequisito::class, 'proyecto_id');
    }

    public function arquitecturas(): HasMany
    {
        return $this->hasMany(Arquitectura::class, 'proyecto_id');
    }

    public function puedeEliminarse(): bool
    {
        if (array_key_exists('arquitecturas_count', $this->getAttributes())) {
            return (int) $this->arquitecturas_count === 0;
        }

        if ($this->relationLoaded('arquitecturas')) {
            return $this->arquitecturas->isEmpty();
        }

        return ! $this->arquitecturas()->exists();
    }
}
