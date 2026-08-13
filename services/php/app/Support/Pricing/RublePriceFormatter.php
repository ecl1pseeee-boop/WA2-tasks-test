<?php

namespace App\Support\Pricing;

class RublePriceFormatter implements PriceFormatterInterface
{
    public function format(float $price): string
    {
        return number_format($price, 2, ',', ' ').' ₽';
    }
}
