<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use PDO;
use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Model\User;

final class MySQLTeamRepository implements TeamRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createTeam(Team $team): int
    {
        $query = <<<'QUERY'
        INSERT INTO teams(team_name, team_score)
        VALUES(:name, :points)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $name = $team->getName();
        $points = $team->getPoints();

        $statement->bindParam('name', $name, PDO::PARAM_STR);
        $statement->bindParam('points', $points, PDO::PARAM_STR);

        $statement->execute();

        $id = $this->databaseConnection->lastInsertId();

        return intval($id);
    }

    public function getTeamById(int $id): ?Team
    {
        $query = <<<'QUERY'
        SELECT * FROM teams WHERE team_id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_INT);

        try {
            $statement->execute();
        } catch (\Exception $e) {
            return null;
        }


        $team = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$team) return null;

        return new Team(
            intval($team['team_id']),
            $team['team_name'],
            intval($team['team_score'])
        );
    }

    public function getTeamByUserId(int $id): ?Team
    {
        $query = <<<'QUERY'
        SELECT t.* FROM teams t
        INNER JOIN team_members tm ON t.team_id = tm.team_id
        WHERE tm.user_id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_INT);

        $statement->execute();

        $team = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$team) return null;

        return new Team(
            intval($team['team_id']),
            $team['team_name'],
            intval($team['team_score'])
        );
    }

    public function getIncompleteTeams(): ?array
    {
        $query = <<<'QUERY'
        SELECT t.* FROM teams t
        LEFT JOIN team_members tm ON t.team_id = tm.team_id
        GROUP BY t.team_id
        HAVING COUNT(tm.user_id) <= 1
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $teams = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$teams) return null;

        $result = [];
        foreach ($teams as $team) {
            $result[] = new Team(
                intval($team['team_id']),
                $team['team_name'],
                intval($team['team_score'])
            );
        }

        return $result;
    }

    public function getTeamNumberOfMembers(int $teamId): int
    {
        $query = <<<'QUERY'
        SELECT COUNT(user_id) FROM team_members WHERE team_id = :team_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('team_id', $teamId, PDO::PARAM_INT);

        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return intval($result['COUNT(user_id)']);
    }

    public function addMemberToTeam(int $teamId, int $userId): void
    {
        $query = <<<'QUERY'
        INSERT INTO team_members(team_id, user_id)
        VALUES(:team_id, :user_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('team_id', $teamId, PDO::PARAM_INT);
        $statement->bindParam('user_id', $userId, PDO::PARAM_INT);

        $statement->execute();
    }

    // Sum the team score
    public function sumTeamScore(int $teamId, int $points): void
    {
        $query = <<<'QUERY'
        UPDATE teams SET team_score = team_score + :points WHERE team_id = :team_id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('team_id', $teamId, PDO::PARAM_INT);
        $statement->bindParam('points', $points, PDO::PARAM_INT);

        $statement->execute();
    }
}
