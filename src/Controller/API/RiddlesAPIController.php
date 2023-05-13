<?php

namespace Salle\PuzzleMania\Controller\API;

use Salle\PuzzleMania\Model\Riddle;
use Salle\PuzzleMania\Repository\RiddleRepository;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RiddlesAPIController
{
    private Twig $twig;
    private RiddleRepository $riddlesRepository;


    public function __construct(Twig $twig, RiddleRepository $riddlesRepository) {
        $this->twig = $twig;
        $this->riddlesRepository = $riddlesRepository;
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

    /********************************* RIDDLE API METHODS ***********************************/
    public function getRiddleEntries(Request $request, Response $response): Response {
        $entries = $this->riddlesRepository->getAllRiddles();
        $responseBody = json_encode($entries);
        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(200);
    }

    public function getRiddleEntry(Request $request, Response $response, array $args): Response {
        // Get riddle id from the request
        $entryId = intval($args['id']);
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
        // Check if the user session exists
        if (!isset($_SESSION['user_id'])) {
            // If it does not, return an error
            $responseBody = <<<body
            {"message": "You must be logged in to create a riddle"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(400);
        }

        // Check if the riddle and answer are set
        if (isset($parsedBody['riddle']) && isset($parsedBody['answer']) &&
            isset($parsedBody['user_id'])) {
            // If the user session exists, create the riddle
            $riddle = $parsedBody['riddle'];
            $answer = $parsedBody['answer'];
            // Create the riddle
            $data = Riddle::create();
            $data->setRiddle($riddle);
            $data->setAnswer($answer);
            $riddleEntry = $this->riddlesRepository->createRiddle($data);

            $response->getBody()->write(json_encode($riddleEntry));
            return $response->withHeader('content-type', 'application/json')->withStatus(201);
        }

        // If the riddle and answer are not set, return an error
        $responseBody = <<<body
        {"message": "'riddle' and/or 'answer' and/or 'userId' key missing"}
        body;
        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(400);
    }

    public function putRiddleEntry(Request $request, Response $response, array $args): Response {
        $parsedBody = $request->getParsedBody();
        $entryId = intval($args['id'] ?? 0);
        // Check if the user session exists
        if (!isset($_SESSION['user_id'])) {
            // If it does not, return an error
            $responseBody = <<<body
            {"message": "You must be logged in to edit a riddle"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(400);
        }

        if (isset($parsedBody['riddle']) && isset($parsedBody['answer']) && !$entryId <= 0) {

        }

        //TODO: Still in progress
        //If something is not in the PUT, return error
        $responseBody = <<<body
        {"message": "'title' and/or 'content' key missing"}
        body;
        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(400);
    }


}