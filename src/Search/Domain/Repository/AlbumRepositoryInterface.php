<?php

namespace App\Search\Domain\Repository;

use App\Search\Domain\Entity\Album;
use App\Search\Infrastructure\Repository\SearchAfter;

interface AlbumRepositoryInterface
{
    public function findById(string $id): ?Album;

    /**
     * @param string[] $ids
     * @return (Album|string)[]
     */
    public function findByIds(array $ids): array;

    //TODO: Do typing instead of array
    public function search(?SearchAfter $searchAfter, string $text, int $count): array;

    /** @param Album[] $albums */
    public function bulkUpsert(array $albums): void;

    public function upsert(Album $album): void;
}