<?php

namespace Salle\PuzzleMania\Controller\API;

use Salle\PuzzleMania\Model\Riddle;
use Salle\PuzzleMania\Repository\RiddleRepository;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RiddlesAPIController
{
    private Twig $twig;
    private RiddleRepository $riddlesRepository;
    private UserRepository $userRepository;


    public function __construct(Twig $twig, RiddleRepository $riddlesRepository, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->riddlesRepository = $riddlesRepository;
        $this->userRepository = $userRepository;
    }

    public function showRiddles(Request $request, Response $response): Response {
        // Get all the exiting riddles from the database
        $riddles = $this->riddlesRepository->getAllRiddles();
        // Check if the array is empty
        if (empty($riddles)) {
            // If it is, show a message saying that there are no riddles
            return $this->twig->render(
                $response,
                'riddles.twig',
                [
                    'message' => 'There are no riddles yet'
                ]
            );
        } else {
            // If it is not, show the riddles
            // Render the riddles page with the riddles
            return $this->twig->render(
                $response,
                'riddles.twig',
                [
                    'array_riddles' => $riddles
                ]
            );
        }
    }

    public function showRiddleById(Request $request, Response $response): Response {
        $riddleId = intval($request->getAttribute('id') ?? 0);

        $riddle = $this->riddlesRepository->getRiddleById($riddleId);
        if ($riddle) {
            $riddles = [$riddle];
            return $this->twig->render(
                $response,
                'riddles.twig',
                [
                    'array_riddles' => $riddles
                ]
            );
        } else {
            // If it does not, show an error
            // Render the riddles page with an error message
            return $this->twig->render(
                $response,
                'riddles.twig',
                [
                    'message' => "Riddle with id $riddleId does not exist"
                ]
            );
        }
    }


    /****************************************************************************************/
    /****************************************************************************************/
    /********************************* RIDDLE API METHODS ***********************************/
    /****************************************************************************************/
    /****************************************************************************************/
    public function getRiddleEntries(Request $request, Response $response): Response {
        $entries = $this->riddlesRepository->getAllRiddles();
        $responseBody = json_encode($entries);
        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(200);
    }

    public function getRiddleEntry(Request $request, Response $response, array $args): Response {
        // Get riddle id from the request
        $entryId = intval($args['id'] ?? 0);
        // Get the riddle from the database
        $entry = $this->riddlesRepository->getRiddleById($entryId);
        // Check if the riddle exists
        if ($entry) {
            // If it does, return the riddle
            $responseBody = json_encode($entry);
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(200);
        } else {
            // If it does not, return an error
            $responseBody = <<<body
            {"message": "Riddle with id $entryId does not exist"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(404);
        }
    }

    public function postRiddleEntry(Request $request, Response $response): Response {
        $parsedBody = $request->getParsedBody();
        // Get the riddle id from the body
        $id = intval($parsedBody['id'] ?? 0);
        // Get the user id from the body
        $userId = intval($parsedBody['userId'] ?? 0);

        // Check if the userId is an existing one or not
        if ($this->userRepository->getUserById($userId) == null) {
            // If the user doesn't exist, return an error
            $responseBody = <<<body
            {"message": "User with id $userId does not exist"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(404);
        }
        // If the user exists, continue

        // Check if the riddle id alredy exisits
        if ($this->riddlesRepository->checkIfRiddleExists($id)) {
            // If it does, return an error
            $responseBody = <<<body
            {"message": "Riddle with id $id already exists"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(400);
        } else {
            // If it does not, continue
            // Check if the riddle and answer are set
            if (isset($parsedBody['riddle']) && isset($parsedBody['answer']) &&
                isset($parsedBody['userId']) && isset($parsedBody['id'])) {
                // Create the riddle
                $data = Riddle::create();
                // If it does not, create the riddle entry
                $data->setId($id);
                $data->setUserId($parsedBody['userId']);
                $data->setRiddle($parsedBody['riddle']);
                $data->setAnswer($parsedBody['answer']);
                $this->riddlesRepository->createRiddle($data);

                $response->getBody()->write(json_encode($data));
                return $response->withHeader('content-type', 'application/json')->withStatus(201);

            } else {
                // Si la riddle, answer, userId o id no están definidos o son nulos, devuelve un error
                $responseBody = <<<body
                {"message": "'riddle' and/or 'answer' and/or 'userId' key missing"}
                body;
                $response->getBody()->write($responseBody);
                return $response->withHeader('content-type', 'application/json')->withStatus(400);
            }
        }
    }

    public function putRiddleEntry(Request $request, Response $response, array $args): Response {
        $parsedBody = $request->getParsedBody();
        // Get the id of the riddle we want to modify.
        $entryId = intval($args['id'] ?? 0);

        if (isset($parsedBody['riddle']) && isset($parsedBody['answer']) && !$entryId <= 0) {
            // Check if the riddle we want to modify exists.
            if($this->riddlesRepository->checkIfRiddleExists($entryId)) {
                // The riddle we want to modify exists, so we modify it.
                $riddle = $parsedBody['riddle'];
                $answer = $parsedBody['answer'];

                if($this->riddlesRepository->modifyRiddleEntry($entryId, $riddle, $answer)){
                    // The riddle was modified successfully.
                    $responseBody = <<<body
                    {"message": "Riddle with id $entryId was modified successfully"}
                    body;
                    $response->getBody()->write($responseBody);
                    return $response->withHeader('content-type', 'application/json')->withStatus(200);
                }

                // The riddle was not modified successfully.
                $responseBody = <<<body
                {"message": "Riddle with id $entryId was not modified successfully"}
                body;
                $response->getBody()->write($responseBody);
                return $response->withHeader('content-type', 'application/json')->withStatus(404);
            }
            // The riddle to modify doesn't exist
            $responseBody = <<<body
            {"message": "Riddle with id $entryId does not exist"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(404);
        }

        //If something is not in the PUT, return error
        $responseBody = <<<body
        {"message": "The riddle and/or answer cannot be empty"}
        body;
        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(400);
    }

    public function deleteRiddleEntry(Request $request, Response $response, array $args){
        $entryId = intval($args['id'] ?? 0);
        if($entryId > 0){
            // Check if the riddle we want to delete exists.
            if($this->riddlesRepository->checkIfRiddleExists($entryId)) {
                // The riddle we want to delete exists, so we delete it.
                if($this->riddlesRepository->deleteRiddleEntry($entryId)){
                    // The riddle was deleted successfully.
                    $responseBody = <<<body
                    {"message": "Riddle with id $entryId was deleted successfully"}
                    body;
                    $response->getBody()->write($responseBody);
                    return $response->withHeader('content-type', 'application/json')->withStatus(200);
                }

                // The riddle was not deleted successfully.
                $responseBody = <<<body
                {"message": "Riddle with id $entryId was not deleted successfully"}
                body;
                $response->getBody()->write($responseBody);
                return $response->withHeader('content-type', 'application/json')->withStatus(404);
            }

            // The riddle to delete doesn't exist
            $responseBody = <<<body
            {"message": "Riddle with id $entryId does not exist"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(404);
        }
    }

}