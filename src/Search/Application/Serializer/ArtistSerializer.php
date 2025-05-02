<?php

namespace App\Search\Application\Serializer;

use App\Search\Domain\Entity\Artist;
use App\Search\Domain\Enum\Source;
use App\Search\Domain\ValueObject\IdSource;
use MusicPlayground\Contract\Application\SongParser\DTO\ArtistDTO;
use App\Search\Domain\Entity\ShortArtist;
use MusicPlayground\Contract\Application\SongParser\DTO\ArtistSourceDTO;
use MusicPlayground\Contract\Application\SongParser\DTO\PreviewArtistDTO;

final class ArtistSerializer
{
    public function fromDto(string $id, ArtistDTO $dto): Artist
    {
        return new Artist($id, $dto->name, $dto->avatarId, $dto->genres);
    }

    public function shortFromDto(string $id, ArtistDTO $dto): ShortArtist
    {
        return new ShortArtist($id, $dto->name, $this->sourceFromDto($dto->source), $dto->avatarId);
    }

    public function sourceFromDto(ArtistSourceDTO $source): IdSource
    {
        return new IdSource($source->id, Source::from($source->name));
    }

    public function shortFromPreviewDto(PreviewArtistDTO $dto): ShortArtist
    {
        return new ShortArtist(
            $dto->id,
            $dto->name,
            $this->sourceFromDto($dto->source),
            $dto->avatarId
        );
    }
}