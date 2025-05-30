<?php

namespace App\Http\Requests;

use App\Rules\CEPValidation;
use App\Rules\CPFValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'cpf' => [
                'sometimes',
                'string',
                'size:11',
                Rule::unique('clients')->ignore($this->client),
                app(CPFValidation::class),
            ],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('clients')->ignore($this->client)
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20'
            ],
            'address' => [
                'sometimes',
                'array'
            ],
            'address.cep' => [
                'required_with:address',
                'digits:8',
                app(CEPValidation::class),
            ],
            'address.street' => [
                'required_with:address',
                'string',
                'max:255'
            ],
            'address.neighborhood' => [
                'required_with:address',
                'string',
                'max:255'
            ],
            'address.city' => [
                'required_with:address',
                'string',
                'max:255'
            ],
            'address.state' => [
                'required_with:address',
                'string',
                'max:100'
            ],
        ];
    }
}
