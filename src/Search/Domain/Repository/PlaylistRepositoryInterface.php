<?php

namespace App\Search\Domain\Repository;

use App\Search\Domain\Entity\Playlist;

interface PlaylistRepositoryInterface
{
    public function upsert(Playlist $playlist): void;

    public function findById(string $id): ?Playlist;
}