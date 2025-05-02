<?php

namespace App\Search\Domain\ValueObject;

use App\Search\Domain\Enum\Source;
use Stringable;

final readonly class IdSource implements Stringable
{
    public const SEPARATOR = ':';

    public function __construct(
        private string $id,
        private Source $name
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name->value;
    }

    public function __toString(): string
    {
        return $this->getName() . self::SEPARATOR . $this->getId();
    }

    public static function from(string $value): self
    {
        [$name, $id] = explode(self::SEPARATOR, $value, 2);

        return new self($id, Source::from($name));
    }

    public function is(IdSource $source): bool {
        return (string)$this === (string)$source;
    }
}