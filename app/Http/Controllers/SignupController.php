<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\Conversations\SignupConversation;

class SignupController extends Controller
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
        $bot->startConversation(new SignupConversation);
    }
}
