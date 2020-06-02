<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\Http\Middleware\CheckForUser;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }
}
