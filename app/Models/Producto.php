<?php

namespace App\Models;

use App\Support\Catalogo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'proveedor_id',
        'region_id',
        'tipo_id',
        'nombre',
        'descripcion',
        'precio',
        'disponible',
        'protegido',
    ];

    protected function casts(): array
    {
        return [
            'proveedor_id' => 'integer',
            'region_id' => 'integer',
            'tipo_id' => 'integer',
            'precio' => 'integer',
            'disponible' => 'boolean',
            'protegido' => 'boolean',
        ];
    }

    public function arquitecturas(): BelongsToMany
    {
        return $this->belongsToMany(Arquitectura::class, 'arquitectura_producto');
    }

    public function presupuestos(): BelongsToMany
    {
        return $this->belongsToMany(Presupuesto::class, 'presupuesto_producto');
    }

    public function getProveedorNombreAttribute(): string
    {
        return Catalogo::proveedor($this->proveedor_id) ?? '';
    }

    public function getRegionNombreAttribute(): string
    {
        return Catalogo::region($this->region_id) ?? '';
    }

    public function getTipoNombreAttribute(): string
    {
        return Catalogo::tipo($this->tipo_id) ?? '';
    }

    public function estaAsociado(): bool
    {
        if ($this->relationLoaded('arquitecturas') && $this->relationLoaded('presupuestos')) {
            return $this->arquitecturas->isNotEmpty() || $this->presupuestos->isNotEmpty();
        }

        return $this->arquitecturas()->exists() || $this->presupuestos()->exists();
    }

    public function puedeEditarse(): bool
    {
        return ! $this->protegido && ! $this->estaAsociado();
    }

    public function puedeEliminarse(): bool
    {
        return $this->puedeEditarse();
    }
}
