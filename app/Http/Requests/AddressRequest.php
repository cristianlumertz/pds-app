<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:30'],
            'complement' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'size:2'],
            'zip_code' => ['required', 'regex:/^\d{8}$/'],
            'country' => ['required', 'string', 'max:80'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'street.required' => 'O campo rua é obrigatório.',
            'number.required' => 'O campo número é obrigatório.',
            'city.required' => 'O campo cidade é obrigatório.',
            'state.required' => 'O campo estado é obrigatório.',
            'state.size' => 'O estado deve conter 2 caracteres (UF).',
            'zip_code.required' => 'O campo CEP é obrigatório.',
            'zip_code.regex' => 'O CEP deve conter 8 dígitos.',
            'country.required' => 'O campo país é obrigatório.',
            'is_default.boolean' => 'O campo endereço padrão é inválido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $zipCode = preg_replace('/\D/', '', (string) $this->input('zip_code', ''));

        $this->merge([
            'zip_code' => $zipCode,
            'state' => strtoupper((string) $this->input('state', '')),
            'is_default' => $this->boolean('is_default'),
        ]);
    }
}
