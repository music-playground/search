<?php

namespace App\Search\Infrastructure\Controller;

use App\Search\Domain\Repository\CollectionRepositoryInterface;
use App\Search\Infrastructure\Controller\Trait\ValidationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/collections')]
class CollectionController extends AbstractController
{
    use ValidationTrait;

    public function __construct(
        private readonly CollectionRepositoryInterface $repository,
        private readonly int $maxQLength,
        private readonly int $maxCount
    ) {
    }

    #[Route('/search', methods: 'GET')]
    public function search(
        #[MapQueryParameter] string $q,
        #[MapQueryParameter] ?int $count,
        #[MapQueryParameter] ?float $afterScore = null,
        #[MapQueryParameter] ?string $afterId = null,
    ): JsonResponse {
        $this->validateQ($q, $this->maxQLength);

        $searchAfter = $this->validateAfterParams($afterScore, $afterId);

        return $this->json($this->repository->search($searchAfter, $q, min(max($count ?? $this->maxCount, 0), $this->maxCount)));
    }
}