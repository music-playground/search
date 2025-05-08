<?php

namespace App\Search\Infrastructure\Repository;

use App\Search\Domain\Entity\Album;
use App\Search\Domain\Repository\AlbumRepositoryInterface;
use App\Search\Infrastructure\Mapping\AlbumMapping;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

final readonly class AlbumRepository implements AlbumRepositoryInterface
{
    public function __construct(
        private Client $client,
        private AlbumMapping $mapping,
        private string $index
    ) {
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function findById(string $id): ?Album
    {
        try {
            ['_source' => $source] = $this->client->get([
                'index' => $this->index,
                'id' => $id
            ])->asArray();
        } catch (ClientResponseException $exception) {
            if ($exception->getCode() === 404) {
                return null;
            }

            throw $exception;
        }

        return $this->mapping->fromArray($source);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function findByIds(array $ids): array
    {
        ['docs' => $docs] = $this->client->mget([
            'index' => $this->index,
            'body' => [
                'docs' => array_map(fn (string $id) => ['_id' => $id], $ids)
            ]
        ])->asArray();

        return array_map(function (array $params) {
            return $params['found'] === true
                ? $this->mapping->fromArray($params['_source'])
                : $params['_id'];
        }, $docs);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulkUpsert(array $albums): void
    {
        $params = [];

        foreach ($albums as $album) {
            $id = $album->getId();
            $doc = $this->mapping->toArray($album);
            $params['body'][] = [
                'update' => [
                    '_id' => $id,
                    '_index' => $this->index
                ]
            ];
            $params['body'][] = [
                'doc' => $doc,
                'doc_as_upsert' => true
            ];
        }

        $this->client->bulk($params);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function upsert(Album $album): void
    {
        $params = $this->mapping->toArray($album);

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