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
        return $this->twig->render($response, 'riddles.twig');

    }

    /********************************* RIDDLES API METHODS ***********************************/
    public function getAllRiddleEntries(Request $request, Response $response): Response {
        $entries = $this->riddlesRepository->getAllRiddles();
        $responseBody = json_encode($entries);
        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(200);
    }


}