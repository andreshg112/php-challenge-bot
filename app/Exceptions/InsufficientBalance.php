<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalance extends Exception
{
    public function __construct(float $amount, float $balance, string $currency)
    {
        parent::__construct(
            "You cannot withdraw {$amount} {$currency} because your balance is"
                . " {$balance} {$currency}."
        );
    }
}
