<?php

namespace App\Search\Domain\Entity;

use App\Search\Domain\ValueObject\IdSource;

class ShortArtist
{
    public function __construct(
        private readonly ?string $id,
        private string $name,
        private readonly IdSource $source,
        private string $avatarId
    ) {
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

    public function getSource(): IdSource
    {
        return $this->source;
    }

    public function getAvatarId(): string
    {
        return $this->avatarId;
    }

    public function setAvatarId(string $id): void
    {
        $this->avatarId = $id;
    }
}