<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use BotMan\BotMan\BotMan;

class DepositController extends Controller
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

            $bot->reply(config('app.error_message'));
        }
    }
}
