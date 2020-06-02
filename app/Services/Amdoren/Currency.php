<?php

namespace App\Services\Amdoren;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use App\Exceptions\InvalidCurrency;

class Currency
{
    public static function convert(
        string $from,
        string $to,
        float $amount
    ): float {
        $guzzle = new Client();

        $query = [
            'api_key' => config('services.amdoren.api_key'),
            'from'    => $from,
            'to'      => $to,
            'amount'  => $amount,
        ];

        $response = $guzzle->get(
            config('services.amdoren.api_url') . '/currency.php',
            ['query' => $query]
        );

        /** @var array */
        $contents = json_decode($response->getBody()->getContents(), true);

        // Invalid from (210) or to (260) currency.
        if (in_array($contents['error'], [210, 260])) {
            throw new InvalidCurrency(
                $contents['error'],
                $contents['error_message'],
                $from,
                $to
            );
        }

        if ($contents['error'] !== 0) {
            throw new Exception(
                $contents['error_message'],
                $contents['error']
            );
        }

        return $contents['amount'];
    }

    public static function list(): Collection
    {
        $list = collect(config('amdoren.currencies'));

        return $list;
    }
}
