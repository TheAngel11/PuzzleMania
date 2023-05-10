<?php

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GameRiddlesController
{

    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showRiddle(Request $request, Response $response): Response {
        $riddleId = $request->getAttribute('riddleId');
        $gameId = $request->getAttribute('gameId');

        return $this->twig->render($response, 'game-riddles.twig');
    }


}