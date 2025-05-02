<?php

namespace App\Search\Infrastructure\Repository;

use App\Search\Domain\Entity\Artist;
use App\Search\Domain\Repository\ArtistRepositoryInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class ArtistRepository implements ArtistRepositoryInterface
{
    public function __construct(
        private Client $client,
        private NormalizerInterface $normalizer,
        private string $index = 'artists'
    ) {
    }

    /**
     * @throws ClientResponseException
     * @throws ExceptionInterface
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function upsert(Artist $artist): void
    {
        $params = $this->normalizer->normalize($artist);

        $this->client->update([
            'index' => $this->index,
            'id' => $params['id'],
            'body' => [
                'doc' => array_diff_key($params, ['id' => 1]),
                'doc_as_upsert' => true
            ]
        ]);
    }
}