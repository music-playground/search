<?php

namespace App\Search\Application\Handler;

use App\Search\Domain\Entity\Playlist;
use App\Search\Domain\Repository\PlaylistRepositoryInterface;
use MusicPlayground\Contract\Application\Playlist\Command\UpdateFullPlaylistCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdatePlaylistHandler
{
    public function __construct(
        private PlaylistRepositoryInterface $repository
    ) {
    }

    public function __invoke(UpdateFullPlaylistCommand $command): void
    {
        $playlist = $this->repository->findById($command->id) ?: new Playlist($command->id, $command->name);

        $playlist->setName($command->name);
        $playlist->setDescription($command->description);
        $playlist->setCoverId($command->coverId);

        $this->repository->upsert($playlist);
    }
}