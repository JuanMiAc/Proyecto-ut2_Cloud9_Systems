<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyectoRequisito extends Model
{
    protected $table = 'proyecto_requisitos';

    protected $fillable = [
        'proyecto_id',
        'requisito',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
}
