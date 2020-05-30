<?php

use App\Http\Controllers\BotManController;

/** @var \BotMan\BotMan\BotMan */
$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('Start conversation', BotManController::class . '@startConversation');
