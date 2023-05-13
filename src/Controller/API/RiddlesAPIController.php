<?php

namespace Salle\PuzzleMania\Controller\API;

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
            $responseBody = json_encode(['error' => 'Riddle not found']);
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(404);
        }
    }










}