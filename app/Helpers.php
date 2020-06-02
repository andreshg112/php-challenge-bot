<?php

namespace App;

use Validator;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Services\Amdoren\Currency;

class Helpers
{
    /**
     * It converts a string input to transactions parameters: amount, currency.
     *
     * @param string $input
     * @return array
     */
    public static function inputToTransactionParameters(string $input): array
    {
        $parameters = array_values(
            array_filter(array_map('trim', explode(' ', $input)))
        );

        if (count($parameters) > 2) {
            throw new \Exception(config('app.messages.fallback'));
        }

        $amount = Arr::get($parameters, 0);

        $currency = Arr::get($parameters, 1);

        $currency = isset($currency) ? mb_strtoupper($currency) : null;

        return [$amount, $currency];
    }

    /**
     * Retorna una instancia del Validator para verificar los parámetros de la
     * transacción.
     *
     * @param string $amount
     * @param string|null $currency
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public static function validateTransactionParameters(
        string $amount,
        ?string $currency = null
    ) {
        $currencies = Currency::list();

        return Validator::make(
            compact('amount', 'currency'),
            [
                // bail to verify first that it is numeric.
                'amount'   => ['bail', 'numeric', 'gt:0'],
                'currency' => ['nullable', Rule::in($currencies->keys())],
            ],
            [
                'amount.gt' => 'The amount must be greater than 0.',
            ]
        );
    }
}
