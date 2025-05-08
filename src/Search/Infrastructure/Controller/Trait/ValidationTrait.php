<?php

namespace App\Search\Infrastructure\Controller\Trait;

use App\Search\Domain\Repository\SearchAfter;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ValidationTrait
{
    public function validateQ(string $q, int $maxLength): void
    {
        if (strlen($q) > $maxLength) {
            throw new HttpException(422, 'Query string to long');
        }
    }

    public function validateAfterParams(?float $afterScore, ?string $afterId): ?SearchAfter
    {
        if (($afterScore xor $afterId) === true)  {
            throw new HttpException(422, 'Not all after parameters provided');
        }

        return $afterScore !== null ? new SearchAfter($afterScore, $afterId) : null;
    }
}