<?php

namespace App\Shared\Infrastructure\ElasticSearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Http\Discovery\Psr18Client;
use SensitiveParameter;

final readonly class ClientFactory
{
    public function __construct(
        #[SensitiveParameter]
        private string $hosts
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function create(): Client
    {
        return ClientBuilder::create()
            ->setHosts(explode(',', $this->hosts))
            ->build();
    }
}