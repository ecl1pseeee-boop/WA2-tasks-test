<?php

namespace App\Repositories;

use App\Models\Ad;

class ReadOnlyAdRepository extends EloquentAdRepository
{
    public function save(Ad $ad): Ad
    {
        throw new \RuntimeException('read-only repository: save is not allowed');
    }

    public function delete(int $id): void
    {
        throw new \RuntimeException('read-only repository: delete is not allowed');
    }
}
