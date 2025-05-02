<?php

namespace App\Search\Application\Handler;

use App\Search\Application\Serializer\ArtistSerializer;
use App\Search\Domain\Repository\ArtistRepositoryInterface;
use MusicPlayground\Contract\Application\SongParser\Command\OnUpdateArtistFullCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateArtistHandler
{
    public function __construct(
        private ArtistRepositoryInterface $repository,
        private ArtistSerializer $serializer,
        private UpdateArtistInAlbumsHandler $albumHandler
    ) {
    }

    public function __invoke(OnUpdateArtistFullCommand $command): void
    {
        $artist = $this->serializer->fromDto($command->id, $command->artist);
        $this->repository->upsert($artist);

        //TODO: Replace as independent
        $this->albumHandler->__invoke($command);
    }
}