<?php

namespace App\Search\Infrastructure\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BoolNormalizer implements NormalizerInterface
{

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_bool($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}