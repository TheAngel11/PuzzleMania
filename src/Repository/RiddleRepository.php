<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Riddle;

interface RiddleRepository
{
    public function createRiddle(Riddle $riddle): int;
    public function getRiddleById(int $riddleId): ?Riddle;
    public function getAllRiddles(): array;
    public function getRandomRiddles(): ?array;
    public function getAnswerByQuestion(string $riddle): string;
    public function modifyRiddleEntry(int $riddleId, string $question, string $answer): ?Riddle;
    public function deleteRiddleEntry(int $riddleId): bool;
    public function checkIfRiddleExists(int $id): bool;
}