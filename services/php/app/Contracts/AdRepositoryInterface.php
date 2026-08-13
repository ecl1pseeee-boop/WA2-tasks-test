<?php

namespace App\Contracts;

use App\Models\Ad;
use Illuminate\Support\Collection;

interface AdRepositoryInterface
{
    public function find(int $id): ?Ad;

    public function all(): Collection;

    public function search(string $query): Collection;

    public function save(Ad $ad): Ad;

    public function delete(int $id): void;

    public function exportCsv(): string;

    public function exportPdf(): string;

    public function sendToModeration(int $id): void;
}
