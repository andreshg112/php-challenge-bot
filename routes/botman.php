<?php

use App\Http\Controllers\BotManController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\ChatbotLoginController;
use App\Http\Controllers\CurrencyExchangeController;

/** @var \BotMan\BotMan\BotMan */
$botman = resolve('botman');

$botman->hears('Help', BotManController::class . '@help');

$botman->hears('Hi', BotManController::class . '@hi');

$botman->hears('Login', ChatbotLoginController::class);

$botman->hears('Start conversation', BotManController::class . '@startConversation');

$botman->hears('Signup', SignupController::class);

$botman->hears(
    'Convert {amount} {from} to {to}',
    CurrencyExchangeController::class
);

$botman->fallback(function ($bot) {
    /** @var \BotMan\BotMan\BotMan $bot */
    $bot->reply(
        'Sorry, I did not understand these commands. Here is a list of commands I understand: ...'
    );
});
