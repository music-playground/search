<?php

namespace App\Search\Domain\Repository;

use App\Search\Domain\Entity\Artist;
use App\Search\Infrastructure\Repository\SearchAfter;

interface ArtistRepositoryInterface
{
    public function upsert(Artist $artist): void;

    public function search(?SearchAfter $searchAfter, string $text, int $count): array;
}