<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;

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
        $userData = $bot->userStorage()->find();

        $bot->userStorage()->delete();

        $bot->reply(
            "{$userData->get('name')}, good bye! Hope to see you soon."
                . ' Type "login" whenever you want to enter again.'
        );
    }
}
