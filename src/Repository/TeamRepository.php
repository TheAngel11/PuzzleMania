<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Team;

interface TeamRepository
{
    public function createTeam(Team $team): int;

    public function getTeamById(int $id): ?Team;

    public function getTeamByUserId(int $id): ?Team;

    public function getIncompleteTeams();

    public function getTeamNumberOfMembers(int $teamId): int;

    public function addMemberToTeam(int $teamId, int $userId): void;

    public function sumTeamScore(int $teamId, int $points): void;
}
