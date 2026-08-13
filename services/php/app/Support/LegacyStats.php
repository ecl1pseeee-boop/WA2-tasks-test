<?php

namespace App\Support;

use PDO;

class LegacyStats
{
    /**
     * Возвращает количество объявлений в БД.
     *
     * Используется в GET /stats.
     */
    public static function adsCount(): int
    {
        $pdo = new PDO('mysql:host=mysql;dbname=boardy', 'boardy', 'boardy');

        $stmt = $pdo->query('SELECT COUNT(*) FROM ads');

        $count = (int) $stmt->fetchColumn();

        Metrics::$adsCreated++;

        return $count;
    }
}
