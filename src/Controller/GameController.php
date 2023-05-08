<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GameController
{
    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showGame(Request $request, Response $response): Response {
        return $this->twig->render($response, 'game.twig');

    }

}