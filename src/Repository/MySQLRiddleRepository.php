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

    public function createRiddle(Riddle $riddle): void
    {
        // Prepare the query
        $query = <<<'QUERY'
        INSERT INTO riddles(riddle_id, user_id, riddle, answer)
        VALUES(:id, :userID, :riddle, :answer)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        // Prepare the binding of the parameters
        $id = $riddle->getId();
        $userID = $riddle->getUserId();
        $question = $riddle->getRiddle();
        $answer = $riddle->getAnswer();
        // Bind the parameters
        $statement->bindParam('id', $id, PDO::PARAM_INT);
        $statement->bindParam('userID', $userID, PDO::PARAM_INT);
        $statement->bindParam('riddle', $question, PDO::PARAM_STR);
        $statement->bindParam('answer', $answer, PDO::PARAM_STR);

        $statement->execute();
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
                $riddle->setRiddle($row['riddle']);
                $riddle->setAnswer($row['answer']);
                $riddle->setId(intval($row['riddle_id']));
                $riddle->setUserId(intval($row['user_id']));
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

    public function getRiddleById(int $riddleId): ?Riddle
    {
        // Prepare the query
        $query = <<<'QUERY'
        SELECT * FROM riddles WHERE riddle_id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $riddleId, PDO::PARAM_INT);
        $statement->execute();

        $count = $statement->rowCount();
        // If there are no riddles, return an empty array
        if ($count <= 0) return null;
        else {
            $row = $statement->fetch();
            $riddle = Riddle::create();
            $riddle->setRiddle($row['riddle']);
            $riddle->setAnswer($row['answer']);
            return $riddle;
        }
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

    public function modifyRiddleEntry(int $riddleId, string $question, string $answer): bool
    {
        // Prepare the query
        // Take the corresponding riddle and update it
        $query = <<<'QUERY'
        UPDATE riddles 
        SET riddle = :question, answer = :answer WHERE riddle_id = :id
        QUERY;
        // Bind all the parameters specified in the query
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('question', $question, PDO::PARAM_STR);
        $statement->bindParam('answer', $answer, PDO::PARAM_STR);
        $statement->bindParam('id', $riddleId, PDO::PARAM_INT);
        $statement->execute();
        // If the riddle has been updated successfully return true, otherwise return false
        return $statement->rowCount() > 0;
    }

    public function deleteRiddleEntry(int $riddleId): bool
    {
        //Prepare the query
        $query = <<<'QUERY'
        DELETE FROM riddles WHERE riddle_id = :id
        QUERY;
        // Bind all the parameters specified in the query
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $riddleId, PDO::PARAM_INT);
        $statement->execute();
        // If the riddle has been deleted successfully return true, otherwise return false
        return $statement->rowCount() > 0;
    }

    public function checkIfRiddleExists(int $id): bool
    {
        //Prepare query
        $query = <<<'QUERY'
        SELECT * FROM riddles WHERE riddle_id = :id
        QUERY;
        // Bind all the parameters specified in the query
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_INT);
        $statement->execute();
        // If the riddle exists return true, otherwise return false
        if ($statement->rowCount() > 0) return true;
        else return false;
    }
}