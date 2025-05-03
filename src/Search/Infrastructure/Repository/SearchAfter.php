<?php

namespace App\Search\Infrastructure\Repository;

final readonly class SearchAfter
{
    public function __construct(
        public float $score,
        public string $id
    ) {
    }
}