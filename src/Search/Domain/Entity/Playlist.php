<?php

namespace App\Search\Domain\Entity;

class Playlist
{
    private ?string $coverId = null;
    private ?string $description = null;

    public function __construct(
        private readonly string $id,
        private string $name,
    ) {
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setCoverId(?string $coverId): void
    {
        $this->coverId = $coverId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCoverId(): ?string
    {
        return $this->coverId;
    }
}