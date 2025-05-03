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
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(?SearchAfter $searchAfter, string $text, int $count): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'size' => $count,
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match' => [
                                    'name' => ['query' => $text, 'boost' => 1.25]
                                ]
                            ],
                            [
                                'match' => [
                                    'artists.name' => ['query' => $text]
                                ]
                            ],
                            [
                                'match' => [
                                    'artists.name' => ['query' => $text, 'boost' => 0.5]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    ['_score' => 'desc'],
                    ['id' => 'desc']
                ]
            ],
        ];

        if ($searchAfter !== null) {
            $params['body']['search_after'] = [$searchAfter->score, $searchAfter->id];
        }

        $response = $this->client->search($params)->asArray();

        return $this->mapSearchResponse($response);
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
        $this->client->update([
           'index' => $this->index,
           'id' => $album->getId(),
           'body' => [
               'doc' => $this->mapping->toArray($album),
               'doc_as_upsert' => true
           ]
        ]);
    }

    private function mapSearchResponse(array $data): array
    {
        return [
            'count' => $data['hits']['total']['value'],
            'items' => array_map(function (array $item) {
                $album = $item['_source'];

                return [
                    'score' => $item['_score'],
                    ...array_diff_key($album, ['full' => 1, 'tracks' => 1]),
                    'artists' => array_map(fn (array $artist) => array_diff_key($artist, ['source' => 1]), $album['artists']),
                ];
            }, $data['hits']['hits'])
        ];
    }
}