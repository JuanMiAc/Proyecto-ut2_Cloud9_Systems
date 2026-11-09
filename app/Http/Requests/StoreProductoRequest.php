<?php

namespace App\Http\Requests;

use App\Support\Catalogo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'integer', Rule::in(Catalogo::proveedorIds())],
            'region_id' => ['required', 'integer', Rule::in(Catalogo::regionIds())],
            'tipo_id' => ['required', 'integer', Rule::in(Catalogo::tipoIds())],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:30'],
            'precio' => ['required', 'integer', 'min:0'],
            'disponible' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $proveedorId = (int) $this->input('proveedor_id');
            $regionId = (int) $this->input('region_id');

            if ($proveedorId && $regionId && ! Catalogo::regionCompatible($proveedorId, $regionId)) {
                $validator->errors()->add('region_id', 'La región no es compatible con el proveedor seleccionado.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'disponible' => $this->boolean('disponible'),
        ]);
    }
}
