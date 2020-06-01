<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\Http\Middleware\CheckForUser;
use App\Conversations\ExampleConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        /** @var \BotMan\BotMan\BotMan */
        $botman = app('botman');

        $botman->middleware->heard(new CheckForUser);

        $botman->listen();
    }

    public function help(BotMan $bot)
    {
        $bot->reply(
            'You can type the next commands:'
                . ' 1) "convert X USD to COP"'
                . ' to convert X dollars to Colombian pesos. You can use almost'
                . ' any currency code'
                . ' | 2) "signup" to register your information.'
                . ' | 3) "login" to enter and start registering transactions.'
                . ' | 4) "deposit X" to put X amount of money in your account.'
                . ' | 5) "withdraw X" to extract X amount of money from your'
                . ' account.'
                . ' | 6) "balance" to see your current account balance.'
                . ' | 7) "logout" to exit.'
        );
    }

    public function hi(BotMan $bot)
    {
        $appName = config('app.name');

        $bot->reply(
            "Hello! My name is {$appName}. I can help you with some monetary"
                . ' operations. Type "help" in any moment and I will show you'
                . ' what I can do.'
        );
    }

    /**
     * Loaded through routes/botman.php
     * @param \BotMan\BotMan\BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }
}
