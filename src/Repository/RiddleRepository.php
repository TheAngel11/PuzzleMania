<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Riddle;

interface RiddleRepository
{
    public function createRiddle(Riddle $riddle): bool;

    public function getAllRiddles(): array;

    public function getRandomRiddles(): array;
}

