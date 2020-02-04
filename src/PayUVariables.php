<?php

namespace TooPago\Payu;

class PayUVariables
{
    public static function getAvailableCreditCards()
    {
        return [
            'VISA',
            'AMEX',
            'MASTERCARD'
        ];
    }
}
