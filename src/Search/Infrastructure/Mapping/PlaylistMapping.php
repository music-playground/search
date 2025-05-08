<?php

namespace App\Search\Infrastructure\Mapping;

use App\Search\Domain\Entity\Playlist;
use Closure;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class PlaylistMapping
{
    public function __construct(
        private SerializerInterface $serializer
    ) {
    }

    public function fromArray(array $params): Playlist
    {
        $playlist = new Playlist($params['id']);

        $setter = function (array $params) {
            /** @var mixed $playlistThis */
            $playlistThis = $this;

            $playlistThis->isFull = $params['full'];
            $playlistThis->name = $params['name'] ?? null;
            $playlistThis->coverId = $params['coverId'] ?? null;
            $playlistThis->description = $params['description'] ?? null;
            $playlistThis->tracks = $params['tracks'] ?? null;
        };

        Closure::bind($setter, $playlist, $playlist::class)($params);

        return $playlist;
    }

    public function toArray(Playlist $playlist): array
    {
        return json_decode($this->serializer->serialize($playlist, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true
        ]), true);
    }
}