<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Support\Facades\Auth;

class ChatbotLogoutController extends Controller
{
    /**
     * Loaded through routes/botman.php
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @param integer|float $amount
     * @param string $from
     * @param string $to
     */
    public function __invoke(BotMan $bot)
    {
        if (Auth::guest()) {
            $bot->reply('There is no logged-in user.');

            return;
        }

        $userData = $bot->userStorage()->find();

        $bot->userStorage()->delete();

        $bot->reply(
            "{$userData->get('name')}, good bye! Hope to see you soon."
                . ' Type "login" whenever you want to enter again.'
        );
    }
}
