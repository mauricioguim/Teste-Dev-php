<?php

namespace App\Http\Requests;

use App\Rules\CEPValidation;
use App\Rules\CPFValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
                'required',
                'string',
                'max:255'
            ],
            'cpf' => [
                'required',
                'string',
                'size:11',
                'unique:clients',
                new CPFValidation()
            ],
            'email' => [
                'required',
                'email',
                'unique:clients'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20'
            ],
            'address' => [
                'required',
                'array'
            ],
            'address.cep' => [
                'required',
                'digits:8',
                new CEPValidation(),
            ],
            'address.street' => [
                'required',
                'string',
                'max:255'
            ],
            'address.neighborhood' => [
                'required',
                'string',
                'max:255'
            ],
            'address.city' => [
                'required',
                'string',
                'max:255'
            ],
            'address.state' => [
                'required',
                'string',
                'max:100'
            ],
        ];
    }
}
