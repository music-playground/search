<?php

namespace App\Search\Infrastructure\Repository;

use App\Search\Domain\Entity\Playlist;
use App\Search\Domain\Repository\PlaylistRepositoryInterface;
use App\Search\Infrastructure\Mapping\PlaylistMapping;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

final readonly class PlaylistRepository implements PlaylistRepositoryInterface
{
    public function __construct(
        private Client $client,
        private PlaylistMapping $mapping,
        private string $index
    ) {
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function upsert(Playlist $playlist): void
    {
        $params = $this->mapping->toArray($playlist);

        $this->client->update([
            'index' => $this->index,
            'id' => $params['id'],
            'body' => [
                'doc' => $params,
                'doc_as_upsert' => true
            ]
        ]);
    }

    public function findById(string $id): ?Playlist
    {
        return null;
    }
}