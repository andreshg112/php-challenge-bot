<?php

namespace App\Http\Controllers;

use Auth;
use BotMan\BotMan\BotMan;

class BalanceController extends Controller
{
    /**
     * Loaded through routes/botman.php
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @param integer|float $amount
     */
    public function __invoke(BotMan $bot)
    {
        if (Auth::guest()) {
            $bot->reply(config('app.messages.must_login'));

            return;
        }

        $user = Auth::user();

        /** @var \App\User $user */
        $bot->reply(
            "Your current balance is {$user->balance} {$user->currency}."
        );
    }
}
