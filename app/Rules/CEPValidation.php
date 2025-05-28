<?php

namespace App\Rules;

use App\Adapters\BrasilApi;
use Closure;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CEPValidation implements ValidationRule
{
    /**
     * The BrasilApi instance to validate CEPs.
     *
     * @var BrasilApi
     */
    private BrasilApi $brasilApi;

    /**
     * Create a new validation rule instance.
     */
    public function __construct(
    ) {
        $this->brasilApi = new BrasilApi();
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     * @throws GuzzleException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cep = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cep) !== 8) {
            $fail('The :attribute must be exactly 8 digits.');
            return;
        }

        try {
            $result = $this->brasilApi->getCep($cep);

            if ($result === null) {
                $fail('The :attribute is not a valid CEP.');
            }
        } catch (\Exception $e) {
            $fail('Unable to validate :attribute. Please try again later.');
        }
    }
}
