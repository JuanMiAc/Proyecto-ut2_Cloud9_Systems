<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyectoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string'],
            'presupuesto' => ['required', 'integer', 'min:0'],
            'estado' => ['sometimes', 'boolean'],
            'requisitos' => ['nullable', 'array'],
            'requisitos.*' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'estado' => $this->boolean('estado', true),
        ]);
    }
}
