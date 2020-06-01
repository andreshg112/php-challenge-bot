<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use BotMan\BotMan\BotMan;
use App\Exceptions\InsufficientBalance;

class WithdrawController extends Controller
{
    /**
     * Loaded through routes/botman.php
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @param integer|float $amount
     */
    public function __invoke(BotMan $bot, $amount)
    {
        if (Auth::guest()) {
            $bot->reply('Sorry! You must login first. Type "login".');

            return;
        }

        $validator = Validator::make(
            compact('amount'),
            [
                // bail to verify first that it is numeric.
                'amount' => ['bail', 'numeric', 'gt:0'],
            ],
            [
                'amount.gt' => 'The amount to withdraw must be greater than 0.',
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
            $user->withdraw($amount);

            $message = "{$amount} {$user->currency} was extracted from your"
                . " account. Your balance is {$user->balance}"
                . " {$user->currency}.";

            $bot->reply($message);
        } catch (\Throwable $th) {
            if ($th instanceof InsufficientBalance) {
                $bot->reply($th->getMessage());

                return;
            }

            report($th);

            $bot->reply(config('app.error_message'));
        }
    }
}
