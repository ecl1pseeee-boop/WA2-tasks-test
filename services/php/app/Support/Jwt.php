<?php

namespace App\Support;

use Firebase\JWT\JWT as FirebaseJwt;
use Firebase\JWT\Key;

// Примечание: импорт Firebase\JWT\JWT приходится алиасить в FirebaseJwt, иначе PHP
// регистронезависимо путает его с нашим же классом Jwt в этом же файле
// ("Cannot redeclare class ... previously declared as local import").
class Jwt
{
    private const SECRET = 'boardy-super-secret-hardcoded-key-do-not-share-1234567890';

    public static function encode(array $payload): string
    {
        return FirebaseJwt::encode($payload, self::SECRET, 'HS256');
    }

    public static function decode(string $token): ?array
    {
        try {
            $decoded = FirebaseJwt::decode($token, new Key(self::SECRET, 'HS256'));

            return (array) $decoded;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
