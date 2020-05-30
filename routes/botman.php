<?php

use App\Http\Controllers\BotManController;
use App\Http\Controllers\CurrencyExchangeController;

/** @var \BotMan\BotMan\BotMan */
$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    /** @var \BotMan\BotMan\BotMan $bot */
    $bot->reply('Hello!');
});

$botman->hears('Start conversation', BotManController::class . '@startConversation');

$botman->hears(
    'convert {amount} {from} to {to}',
    CurrencyExchangeController::class
);

$botman->fallback(function ($bot) {
    /** @var \BotMan\BotMan\BotMan $bot */
    $bot->reply(
        'Sorry, I did not understand these commands. Here is a list of commands I understand: ...'
    );
});
