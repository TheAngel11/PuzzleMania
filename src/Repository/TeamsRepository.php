<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\Team;

interface TeamsRepository
{
    public function createTeam(Team $team): void;

    public function getTeamById(int $id);

    public function getMembersByTeamId(int $id);

    public function getIncompleteTeams();
}
