<?php

namespace App\Support\Pricing;

interface PriceFormatterInterface
{
    public function format(float $price): string;
}
