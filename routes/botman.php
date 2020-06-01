<?php

use App\User;
use BotMan\BotMan\BotMan;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\ChatbotLoginController;
use App\Http\Controllers\ChatbotLogoutController;
use App\Http\Controllers\CurrencyExchangeController;

/** @var \BotMan\BotMan\BotMan */
$botman = resolve('botman');

$botman->hears(
    'Convert {amount} {from} to {to}',
    CurrencyExchangeController::class
);

$botman->hears('Balance', BalanceController::class);

$botman->hears('Deposit {amount}', DepositController::class);

$botman->hears('Help', BotManController::class . '@help');

$botman->hears('Hi', BotManController::class . '@hi');

$botman->hears('Login', ChatbotLoginController::class);

$botman->hears('Logout', ChatbotLogoutController::class);

$botman->hears('Signup', SignupController::class);

$botman->hears(
    'Start conversation',
    BotManController::class . '@startConversation'
);

// This endpoint was created just to debug user information.
$botman->hears('User', function (BotMan $bot) {
    /** @var \App\User $user */
    $user = Auth::user() ?? new User();

    $bot->reply($user->toJson());
});

$botman->hears('Withdraw {amount}', WithdrawController::class);

$botman->fallback(function (BotMan $bot) {
    $bot->reply(
        'Sorry, I do not understand these commands. Type "hi" or "help".'
    );
});
