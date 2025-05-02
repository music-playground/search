<?php

namespace App\Search\Infrastructure\Mapping;

use App\Search\Domain\Entity\ShortArtist;
use App\Search\Domain\ValueObject\IdSource;

final readonly class ArtistMapping
{
    public function shortFromArray(array $params): ShortArtist
    {
        return new ShortArtist(
            $params['id'],
            $params['name'],
            IdSource::from($params['source']),
            $params['avatarId']
        );
    }

    /**
     * @return ShortArtist[]
     */
    public function manyShortsFromArray(array $manyParams): array
    {
        return array_map(fn (array $params) => $this->shortFromArray($params), $manyParams);
    }
}