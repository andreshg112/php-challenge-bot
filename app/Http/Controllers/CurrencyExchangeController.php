<?php

namespace App\Http\Controllers;

use Validator;
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

        $notValidCurrencyMessage = 'is not a valid currency code.';

        $validator = Validator::make(
            compact('amount', 'from', 'to'),
            [
                'amount' => ['numeric', 'gt:0'],
                'from'   => ['string', 'size:3'],
                'to'     => ['string', 'size:3'],
            ],
            [
                'amount.gt' => 'The amount to convert must be greater than 0.',
                'from.size' => "{$from} {$notValidCurrencyMessage}",
                'to.size'   => "{$to} {$notValidCurrencyMessage}",
            ]
        );

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            $bot->reply($message);

            return;
        }

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
