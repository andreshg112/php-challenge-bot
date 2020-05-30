<?php

use App\Http\Controllers\BotManController;

/** @var \BotMan\BotMan\BotMan */
$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    /** @var \BotMan\BotMan\BotMan $bot */
    $bot->reply('Hello!');
});

$botman->hears('Start conversation', BotManController::class . '@startConversation');

$botman->hears('convert {amount} {from} to {to}', function ($bot, $amount, $from, $to) {
    /** @var \BotMan\BotMan\BotMan $bot */
    $bot->reply("from: {$from}, to: {$to}, amount: {$amount}");
});
