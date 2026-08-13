<?php

namespace App\Repositories;

use App\Contracts\AdRepositoryInterface;
use App\Models\Ad;
use Illuminate\Support\Collection;

class EloquentAdRepository implements AdRepositoryInterface
{
    public function find(int $id): ?Ad
    {
        return Ad::find($id);
    }

    public function all(): Collection
    {
        return Ad::all();
    }

    public function search(string $query): Collection
    {
        return Ad::where('title', 'like', "%{$query}%")->get();
    }

    public function save(Ad $ad): Ad
    {
        $ad->save();

        return $ad;
    }

    public function delete(int $id): void
    {
        Ad::destroy($id);
    }

    public function exportCsv(): string
    {
        throw new \BadMethodCallException('exportCsv not implemented');
    }

    public function exportPdf(): string
    {
        throw new \BadMethodCallException('exportPdf not implemented');
    }

    public function sendToModeration(int $id): void
    {
        throw new \BadMethodCallException('sendToModeration not implemented');
    }
}
