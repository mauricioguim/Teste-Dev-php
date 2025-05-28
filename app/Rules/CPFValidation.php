<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class CPFValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isValidCPF($value)) {
            $fail('The :attribute must be a valid CPF number.');
        }
    }

    /**
     * Check if the CPF is valid
     *
     * This method validates a Brazilian CPF number.
     * It checks the length, format, and calculates the verification digits.
     * It returns true if the CPF is valid, false otherwise.
     *
     * @param string $cpf
     * @return bool
     */
    private function isValidCPF(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        $calculaDigito = function (string $cpfParcial, int $pesoInicial): int {
            $soma = 0;
            for ($i = 0; $i < strlen($cpfParcial); $i++) {
                $soma += (int) $cpfParcial[$i] * $pesoInicial--;
            }
            $resto = $soma % 11;
            return ($resto < 2) ? 0 : (11 - $resto);
        };

        $digito1 = $calculaDigito(substr($cpf, 0, 9), 10);
        $digito2 = $calculaDigito(substr($cpf, 0, 9) . $digito1, 11);

        return $digito1 === (int) $cpf[9] && $digito2 === (int) $cpf[10];
    }
}
