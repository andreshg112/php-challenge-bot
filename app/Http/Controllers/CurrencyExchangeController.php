<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use GuzzleHttp\Client;
use BotMan\BotMan\BotMan;
use function GuzzleHttp\json_decode;

class CurrencyExchangeController extends Controller
{
    /**
     * Loaded through routes/botman.php
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @param integer|float $amount
     * @param string $from
     * @param string $to
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

        // Invalid from (210) or to (260) currency.
        if (in_array($contents['error'], [210, 260])) {
            // If error is 210, the wrong curreny is from, else, is to.
            $currency = $contents['error'] === 210 ? $from : $to;

            $errorMessage = trim($contents['error_message'], '.');

            $bot->reply("{$errorMessage}: {$currency}.");

            return;
        }

        if ($contents['error'] !== 0) {
            $exception = new Exception($contents['error_message'], $contents['error']);

            report($exception);

            $bot->reply(config('app.messages.error'));

            return;
        }

        $result = $contents['amount'];

        $bot->reply("{$amount} {$from} = {$result} {$to}");
    }
}
