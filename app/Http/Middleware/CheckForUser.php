<?php

namespace App\Http\Middleware;

use App\User;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Facades\Auth;
use BotMan\BotMan\Interfaces\Middleware\Heard;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class CheckForUser implements Heard
{
    /**
     * Handle a message that was successfully heard, but not processed yet.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function heard(IncomingMessage $message, $next, BotMan $bot)
    {
        $userData = $bot->userStorage()->find();

        $user = User::find($userData->get('id'));

        if (isset($user)) {
            Auth::login($user);
        }

        return $next($message);
    }
}
