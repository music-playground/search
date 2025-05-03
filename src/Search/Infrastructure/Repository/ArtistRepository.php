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
        private string $index
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

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
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
                                    'name' => ['query' => $text]
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
        ];

        if ($searchAfter !== null) {
            $params['body']['search_after'] = [$searchAfter->score, $searchAfter->id];
        }

        $response = $this->client->search($params)->asArray();

        return $this->mapSearchResponse($response);
    }

    private function mapSearchResponse(array $data): array
    {
        return [
            'count' => $data['hits']['total']['value'],
            'items' => array_map(function (array $item) {
                $artist = $item['_source'];

                return [
                    'score' => $item['_score'],
                    ...$artist
                ];
            }, $data['hits']['hits'])
        ];
    }
}