<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Services\Amdoren\Currency;
use App\Exceptions\InvalidCurrency;

class DepositController extends Controller
{
    /**
     * Loaded through routes/botman.php
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @param string $input
     * @return void
     */
    public function __invoke(BotMan $bot, string $input)
    {
        if (Auth::guest()) {
            $bot->reply(config('app.messages.must_login'));

            return;
        }

        /**
         * The $amount and $currency are extracted from the $input after the
         * "deposit" keyword.
         */
        $parameters = array_values(
            array_filter(array_map('trim', explode(' ', $input)))
        );

        if (count($parameters) > 2) {
            $bot->reply(config('app.messages.fallback'));

            return;
        }

        $amount = Arr::get($parameters, 0);

        $currency = mb_strtoupper(Arr::get($parameters, 1));

        $currencies = Currency::list();

        $validator = Validator::make(
            compact('amount', 'currency'),
            [
                // bail to verify first that it is numeric.
                'amount'   => ['bail', 'numeric', 'gt:0'],
                'currency' => ['nullable', Rule::in($currencies->keys())],
            ],
            [
                'amount.gt' => 'The amount to deposit must be greater than 0.',
            ]
        );

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            $bot->reply($message);

            return;
        }

        /** @var \App\User */
        $user = Auth::user();

        $convertedAmount = $amount;

        if (!empty($currency) && $currency !== $user->currency) {
            $convertedAmount = Currency::convert(
                $currency,
                $user->currency,
                $amount
            );

            $bot->reply(
                "{$amount} {$currency} = {$convertedAmount} {$user->currency}"
            );
        }

        try {
            $user->deposit($convertedAmount);

            $message = "{$convertedAmount} {$user->currency} was added to your"
                . " account. Your balance is {$user->balance}"
                . " {$user->currency}.";

            $bot->reply($message);
        } catch (\Throwable $th) {
            if ($th instanceof InvalidCurrency) {
                $bot->reply($th->getMessage());

                return;
            }

            report($th);

            $bot->reply(config('app.messages.error'));
        }
    }
}
