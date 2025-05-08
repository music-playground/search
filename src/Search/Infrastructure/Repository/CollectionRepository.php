<?php

namespace App\Search\Infrastructure\Repository;

use App\Search\Domain\Repository\CollectionRepositoryInterface;
use App\Search\Domain\Repository\SearchAfter;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use RuntimeException;

final readonly class CollectionRepository implements CollectionRepositoryInterface
{
    public function __construct(
        private Client $client,
        private string $albumIndex,
        private string $playlistIndex,
        private string $fileHost,
        private string $albumFileDomain,
        private string $playlistFileDomain
    ) {
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(?SearchAfter $searchAfter, string $text, int $count): array
    {
        $response = $this->client->search([
            'index' => [$this->albumIndex, $this->playlistIndex],
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
            ]
        ])->asArray();

        return $this->mapSearchResponse($response);
    }

    private function mapSearchResponse(array $data): array
    {
        return [
            'count' => $data['hits']['total']['value'],
            'items' => array_map(function (array $item) {
                $type = $item['_index'];
                $source = $item['_source'];

                if ($type === $this->albumIndex) {
                    return [
                        'score' => $item['_score'],
                        'type' => 'albums',
                        ...array_diff_key($source, ['full' => 1, 'tracks' => 1, 'coverId' => 1]),
                        'artists' => array_map(fn (array $artist) => array_diff_key($artist, ['source' => 1]), $source['artists']),
                        'cover' => "$this->fileHost/$this->albumFileDomain/" . $source['coverId']
                    ];
                } elseif ($type === $this->playlistIndex) {
                    return [
                        'score' => $item['_score'],
                        'type' => 'playlists',
                        ...array_diff_key($source),
                        'cover' => "$this->fileHost/$this->playlistFileDomain/" . $source['coverId']
                    ];
                } else {
                    throw new RuntimeException('Invalid index');
                }
            }, $data['hits']['hits'])
        ];
    }
}