<?php

namespace App\Search\Domain\Entity;

use InvalidArgumentException;

class Artist
{
    private array $genres = [];

    public function __construct(
        private readonly string $id,
        private string $name,
        private string $avatarId,
        array $genres
    ) {
        $this->setGenres($genres);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAvatarId(): string
    {
        return $this->avatarId;
    }

    public function setAvatarId(string $id): void
    {
        $this->avatarId = $id;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres): void
    {
        if (array_unique($this->genres) !== $this->genres) {
            throw new InvalidArgumentException('Genres has duplicated');
        }

        $this->genres = $genres;
    }
}