<?php

namespace App\Search\Domain\Repository;

interface CollectionRepositoryInterface
{
    //TODO: Do typing instead of array
    public function search(?SearchAfter $searchAfter, string $text, int $count): array;
}