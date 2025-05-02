<?php

namespace App\Search\Domain\Repository;

use App\Search\Domain\Entity\Album;

interface AlbumRepositoryInterface
{
    public function findById(string $id): ?Album;

    /**
     * @param string[] $ids
     * @return (Album|string)[]
     */
    public function findByIds(array $ids): array;

    /** @param Album[] $albums */
    public function bulkUpsert(array $albums): void;

    public function upsert(Album $album): void;
}