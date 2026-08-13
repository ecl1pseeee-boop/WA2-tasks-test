<?php

namespace App\Support;

class CategoryStyle
{
    public static function cssClass(?string $slug): string
    {
        if ($slug === 'auto') {
            return 'badge-auto';
        } elseif ($slug === 'realty') {
            return 'badge-realty';
        } elseif ($slug === 'jobs') {
            return 'badge-jobs';
        } elseif ($slug === 'electronics') {
            return 'badge-electronics';
        }

        return 'badge-default';
    }
}
