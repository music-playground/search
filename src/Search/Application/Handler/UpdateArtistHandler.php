<?php

namespace App\Search\Application\Handler;

use MusicPlayground\Contract\Application\SongParser\Command\OnUpdateArtistCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateArtistHandler
{
    public function __invoke(OnUpdateArtistCommand $command): void
    {

    }
}