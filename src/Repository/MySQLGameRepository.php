<?php
declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use PDO;
use Salle\PuzzleMania\Model\Game;

class MySQLGameRepository implements GameRepository
{
    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }


    public function createGame(Game $game): int
    {
        $query = <<<'QUERY'
        INSERT INTO games(riddle1, riddle2, riddle3, user_id, score)
        VALUES(:riddle1, :riddle2, :riddle3, :user_id, :score)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $riddle1 = $game->getRiddle1();
        $riddle2 = $game->getRiddle2();
        $riddle3 = $game->getRiddle3();
        $user_id = $game->getUserId();
        $score = $game->getScore();

        $statement->bindParam('riddle1', $riddle1, PDO::PARAM_STR);
        $statement->bindParam('riddle2', $riddle2, PDO::PARAM_STR);
        $statement->bindParam('riddle3', $riddle3, PDO::PARAM_STR);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam('score', $score, PDO::PARAM_INT);

        $statement->execute();

        $id = $this->databaseConnection->lastInsertId();

        return intval($id);
    }

    public function getScore(int $gameId): int
    {
        $query = <<<'QUERY'
        SELECT score FROM games WHERE game_id = :gameId
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('gameId', $gameId, PDO::PARAM_INT);

        $statement->execute();

        $result = $statement->fetch();

        if ($result === false) return 0;

        return intval($result['score']);
    }

    public function updateScore(int $gameId, int $score): void
    {
        $query = <<<'QUERY'
        UPDATE games SET score = :score WHERE game_id = :gameId
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('gameId', $gameId, PDO::PARAM_INT);
        $statement->bindParam('score', $score, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getRiddle(int $gameId, int $riddleId) : string{
        $query = <<<'QUERY'
        SELECT riddle1, riddle2, riddle3 FROM games WHERE game_id = :gameId
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('gameId', $gameId, PDO::PARAM_INT);

        $statement->execute();

        $result = $statement->fetch();

        if ($result === false) return "";

        if($riddleId == 1){
            return $result['riddle1'];
        }else if($riddleId == 2){
            return $result['riddle2'];
        }else if($riddleId == 3){
            return $result['riddle3'];
        }else{
            return "";
        }
    }
}