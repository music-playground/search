<?php

namespace App\Search\Infrastructure\Normalizer;

use App\Search\Domain\ValueObject\IdSource;
use Stringable;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class StringableNormalizer implements NormalizerInterface
{
    /**
     * @param Stringable $data
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return (string)$data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Stringable;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            IdSource::class => true
        ];
    }
}