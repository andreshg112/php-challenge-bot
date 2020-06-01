<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use BotMan\BotMan\BotMan;
use Illuminate\Validation\Rule;
use App\Services\Amdoren\Currency;

class DepositController extends Controller
{
    /**
     * Loaded through routes/botman.php
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @param int|float $amount
     * @param string|null $currency
     * @return void
     */
    public function __invoke(BotMan $bot, $amount, $currency = null)
    {
        if (Auth::guest()) {
            $bot->reply(config('app.messages.must_login'));

            return;
        }

        $currency = mb_strtoupper(trim($currency));

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

        $user = Auth::user();

        try {
            /** @var \App\User $user */
            $user->deposit($amount);

            $message = "{$amount} {$user->currency} was added to your account."
                . " Your balance is {$user->balance} {$user->currency}.";

            $bot->reply($message);
        } catch (\Throwable $th) {
            report($th);

            $bot->reply(config('app.messages.error'));
        }
    }
}
