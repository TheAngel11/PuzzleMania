<?php

namespace Salle\PuzzleMania\Controller\API;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RiddlesAPIController
{
    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showRiddles(Request $request, Response $response): Response {
        return $this->twig->render($response, 'riddles.twig');

    }

}