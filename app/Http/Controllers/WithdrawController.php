<?php

namespace App\Http\Controllers;

use Auth;
use App\Helpers;
use BotMan\BotMan\BotMan;
use App\Services\Amdoren\Currency;
use App\Exceptions\InsufficientBalance;

class WithdrawController extends Controller
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

        try {
            [$amount, $currency] = Helpers::inputToTransactionParameters($input);
        } catch (\Throwable $th) {
            $bot->reply($th->getMessage());

            return;
        }

        $validator = Helpers::validateTransactionParameters($amount, $currency);

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
            /** @var \App\User $user */
            $user->withdraw($convertedAmount);

            $message = "{$convertedAmount} {$user->currency} was extracted from your"
                . " account. Your balance is {$user->balance}"
                . " {$user->currency}.";

            $bot->reply($message);
        } catch (\Throwable $th) {
            if ($th instanceof InsufficientBalance) {
                $bot->reply($th->getMessage());

                return;
            }

            report($th);

            $bot->reply(config('app.messages.error'));
        }
    }
}
