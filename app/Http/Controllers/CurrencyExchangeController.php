<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use BotMan\BotMan\BotMan;

use function GuzzleHttp\json_decode;

class CurrencyExchangeController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        /** @var \BotMan\BotMan\BotMan */
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function __invoke(BotMan $bot, $amount, $from, $to)
    {
        $from = mb_strtoupper($from);

        $to = mb_strtoupper($to);

        $guzzle = new Client();

        $response = $guzzle->get(
            config('services.amdoren.api_url') . '/currency.php',
            [
                'query' => [
                    'api_key' => config('services.amdoren.api_key'),
                    'from'    => $from,
                    'to'      => $to,
                    'amount'  => $amount,
                ],
            ]
        );

        /** @var array */
        $contents = json_decode($response->getBody()->getContents(), true);

        if ($contents['error'] !== 0) {
            $bot->reply($contents['error_message']);

            return;
        }

        $result = $contents['amount'];

        $bot->reply("{$amount} {$from} = {$result} {$to}");
    }
}
