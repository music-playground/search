<?php

namespace App\Search\Domain\Repository;

use App\Search\Domain\Entity\Artist;

interface ArtistRepositoryInterface
{
    public function upsert(Artist $artist): void;
}