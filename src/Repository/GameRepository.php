<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;


use Salle\PuzzleMania\Model\Game;

interface GameRepository
{

    public function createGame(Game $game): int;

    public function getScore(int $gameId): int;

    public function updateScore(int $gameId, int $score): void;

    public function getRiddle(int $gameId, int $riddleId): string;

}
