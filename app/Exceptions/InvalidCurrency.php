<?php

namespace App\Exceptions;

use Exception;

class InvalidCurrency extends Exception
{
    public function __construct(
        int $error,
        string $errorMessage,
        string $from,
        string $to
    ) {
        // If error is 210, the wrong curreny is from, else, is to.
        $currency = $error === 210 ? $from : $to;

        $errorMessage = trim($errorMessage, '.');

        parent::__construct("{$errorMessage}: {$currency}.", $error);
    }
}
