<?php

namespace Salle\PuzzleMania\Repository;

use PDO;
use Salle\PuzzleMania\Model\Riddle;

// Constants
define('NUM_OF_RIDDLES', 3);

class MySQLRiddleRepository implements RiddleRepository
{
    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createRiddle(Riddle $riddle): bool
    {
        // TODO: Implement createRiddle() method.
        return true;
    }

    public function getAllRiddles(): array
    {
        // Prepare the query
        $query = <<<'QUERY'
        SELECT * FROM riddles
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $count = $statement->rowCount();
        // If there are no riddles, return an empty array
        if ($count <= 0) return array();
        else {
            // Create an array of riddles
            $riddles = array();
            while ($row = $statement->fetch()) {
                $riddle = Riddle::create();
                $riddle->setQuestion($row['riddle']);
                $riddle->setAnswer($row['answer']);
                $riddles[] = $riddle;
            }
            return $riddles;
        }
    }

    public function getRandomRiddles(): array
    {
        $riddles = $this->getAllRiddles();

        shuffle($riddles);
        return array_slice($riddles, 0, NUM_OF_RIDDLES);
    }

    public function getAnswerByQuestion(string $riddle): string
    {
        // Prepare the query
        $query = <<<'QUERY'
        SELECT answer FROM riddles WHERE riddle = :riddle
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('riddle', $riddle, PDO::PARAM_STR);
        $statement->execute();

        $count = $statement->rowCount();
        // If there are no riddles, return an empty array
        if ($count <= 0) return "";
        else {
            $row = $statement->fetch();
            return $row['answer'];
        }
    }
}