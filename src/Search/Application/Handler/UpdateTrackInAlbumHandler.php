<?php

namespace App\Search\Application\Handler;

use App\Search\Domain\Entity\Album;
use App\Search\Domain\Entity\ShortTrack;
use App\Search\Domain\Repository\AlbumRepositoryInterface;
use MusicPlayground\Contract\Application\SongParser\Command\OnUpdateTrackCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateTrackInAlbumHandler
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository
    ) {
    }

    public function __invoke(OnUpdateTrackCommand $command): void
    {
        $album = $this->albumRepository->findById($command->albumId) ?: new Album($command->albumId);

        $album->addTrack(new ShortTrack($command->id, $command->name));

        $this->albumRepository->upsert($album);
    }
}