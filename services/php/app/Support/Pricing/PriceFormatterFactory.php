<?php

namespace App\Support\Pricing;

class PriceFormatterFactory
{
    public function make(string $currency = 'RUB'): PriceFormatterInterface
    {
        switch ($currency) {
            case 'RUB':
                return new RublePriceFormatter();
            default:
                throw new \InvalidArgumentException("Неизвестная валюта: {$currency}");
        }
    }
}
