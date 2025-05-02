<?php

namespace App\Search\Application\Handler;

use App\Search\Application\Serializer\ArtistSerializer;
use App\Search\Domain\Entity\Album;
use App\Search\Domain\Entity\ShortArtist;
use App\Search\Domain\Repository\AlbumRepositoryInterface;
use MusicPlayground\Contract\Application\SongParser\Command\OnUpdateArtistFullCommand;

final readonly class UpdateArtistInAlbumsHandler
{
    public function __construct(
        private AlbumRepositoryInterface $repository,
        private ArtistSerializer $artistSerializer,
        private int $bulkChunkSize = 10
    ) {
    }

    public function __invoke(OnUpdateArtistFullCommand $command): void
    {
        $shortArtist = $this->artistSerializer->shortFromDto($command->id, $command->artist);

        foreach (array_chunk($command->containsAlbums, $this->bulkChunkSize) as $chunk) {
            $this->updateAlbum($chunk, $shortArtist);
        }
    }

    /**
     * @param string[] $albumIds
     */
    private function updateAlbum(array $albumIds, ShortArtist $artist): void
    {
        $albums = array_map(function (Album|string $album) use ($artist) {
            $album = $album instanceof Album ? $album : new Album($album);

            $album->addArtist($artist);

            return $album;
        }, $this->repository->findByIds($albumIds));

        $this->repository->bulkUpsert($albums);
    }
}