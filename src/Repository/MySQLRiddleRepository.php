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
                $riddle->setQuestion($row['question']);
                $riddle->setAnswer($row['answer']);
                array_push($riddles, $riddle);
            }
            return $riddles;
        }
    }

    public function getRandomRiddles(): array
    {
        $riddles = $this->getAllRiddles();
        $randomRiddles = array();

        // Return 3 random riddles form the array of riddles
        for ($i = 0; $i < NUM_OF_RIDDLES; $i++) {
            $randomRiddle = $riddles[array_rand($riddles)];
            array_push($randomRiddles, $randomRiddle);
        }
        return $randomRiddles;
    }

    public function getAnswerByQuestion(string $question): string
    {
        // Prepare the query
        $query = <<<'QUERY'
        SELECT answer FROM riddles WHERE question = :question
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('question', $question, PDO::PARAM_STR);
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