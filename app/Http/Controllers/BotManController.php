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
        $bot->reply(config('app.messages.help'));
    }

    public function hi(BotMan $bot)
    {
        $bot->reply(config('app.messages.hi'));
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
