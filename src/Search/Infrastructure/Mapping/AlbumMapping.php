<?php

namespace App\Search\Infrastructure\Mapping;

use App\Search\Domain\Entity\Album;
use App\Search\Domain\Entity\ShortTrack;
use Closure;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class AlbumMapping
{
    public function __construct(
        private SerializerInterface $serializer,
        private ArtistMapping $artistMapping
    ) {
    }

    public function fromArray(array $params): Album
    {
        $album = new Album($params['id']);

        $setter = function (array $params, ArtistMapping $mapping) {
            /** @var mixed $albumThis */
            $albumThis = $this;

            $albumThis->isFull = $params['full'];
            $albumThis->name = $params['name'] ?? null;
            $albumThis->coverId = $params['coverId'] ?? null;
            $albumThis->genres = $params['genres'] ?? null;
            $albumThis->tracks = isset($params['tracks'])
                ? array_map(fn (array $track) => new ShortTrack($track['id'], $track['name']), $params['tracks'])
                : null;
            $albumThis->artists = isset($params['artists'])
                ? $mapping->manyShortsFromArray($params['artists'])
                : null;

        };

        Closure::bind($setter, $album, $album::class)($params, $this->artistMapping);

        return $album;
    }

    public function toArray(Album $album): array
    {
        return json_decode($this->serializer->serialize($album, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true
        ]), true);
    }
}