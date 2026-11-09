<?php

namespace App\Http\Requests;

use App\Support\Catalogo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePresupuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'tipo_pago' => ['required', 'string', Rule::in(Catalogo::TIPOS_PAGO)],
        ];
    }
}
