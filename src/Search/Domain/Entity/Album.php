<?php

namespace App\Search\Domain\Entity;

class Album
{
    private bool $isFull = true;
    private ?string $name = null;
    private ?string $coverId = null;
    private ?array $genres = null;
    /** @var ShortTrack[]|null */
    private ?array $tracks = null;
    /** @var ShortArtist[]|null */
    private ?array $artists = null;

    public function __construct(private readonly string $id) {
    }

    public function setFull(string $name, string $coverId): void {
        $this->name = $name;
        $this->coverId = $coverId;
        $this->isFull = true;
    }

    public function addArtist(ShortArtist $newArtist): void
    {
        $this->artists = $this->artists ?: [];

        foreach ($this->artists as &$artist) {
            if ($artist->getSource()->is($newArtist->getSource()) === true) {
                $artist = $newArtist;
                return;
            }
        }

        $this->artists []= $newArtist;
    }

    public function addArtistWithoutReplacement(ShortArtist $newArtist): void
    {
        $this->artists = $this->artists ?: [];

        foreach ($this->artists as &$artist) {
            if ($artist->getSource()->is($newArtist->getSource()) === true) {
                if ($artist->getId() === null) {
                    $artist = $newArtist;
                }

                return;
            }
        }
        $this->artists []= $newArtist;
    }

    public function setGenres(array $genres): void
    {
        $this->genres = $genres;
    }

    public function addTrack(ShortTrack $newTrack): void
    {
        $this->tracks = $this->tracks ?: [];

        foreach ($this->tracks as &$track) {
            if ($track->getId() === $newTrack->getId()) {
                $track = $newTrack;
                return;
            }
        }

        $this->tracks []= $newTrack;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCoverId(): ?string
    {
        return $this->coverId;
    }

    /** @return ShortArtist[]|null */
    public function getArtists(): ?array
    {
        return $this->artists;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isFull(): bool
    {
        return $this->isFull;
    }

    public function getGenres(): ?array
    {
        return $this->genres;
    }

    public function getTracks(): ?array
    {
        return $this->tracks;
    }
}