<?php

namespace App\Search\Application\Handler;

use App\Search\Application\Serializer\ArtistSerializer;
use App\Search\Domain\Entity\Album;
use App\Search\Domain\Entity\ShortArtist;
use App\Search\Domain\Repository\AlbumRepositoryInterface;
use MusicPlayground\Contract\Application\SongParser\Command\OnUpdateAlbumFullCommand;
use MusicPlayground\Contract\Application\SongParser\DTO\PreviewArtistDTO;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateFullAlbumHandler
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ArtistSerializer $artistSerializer
    ) {
    }

    public function __invoke(OnUpdateAlbumFullCommand $command): void
    {
        $albumData = $command->album;
        $artists = array_map(
            fn (PreviewArtistDTO $dto) => $this->artistSerializer->shortFromPreviewDto($dto),
            $albumData->artists
        );

        $album = $this->albumRepository->findById($albumData->id) ?: new Album($albumData->id);

        $album->setFull($albumData->name, $albumData->cover);
        $album->setGenres($albumData->genres);
        array_walk($artists, fn (ShortArtist $artist) => $album->addArtistWithoutReplacement($artist));

        $this->albumRepository->upsert($album);
    }
}