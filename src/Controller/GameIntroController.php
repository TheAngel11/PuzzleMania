<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GameIntroController
{
    private Twig $twig;
    private int $gameId;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
        $this->gameId = 0;
    }

    public function showGame(Request $request, Response $response): Response {
        // TODO: Check if the user has joined a team

        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('game');


        return $this->twig->render($response, 'game-intro.twig', [
            'groupName' => "HARDCODED",
            'formAction' => $formAction
        ]);
    }

    public function gameAction(Request $request, Response $response): Response {
        $this->gameId++;

        return $response->withHeader('Location', "/game/$this->gameId/riddle/1")->withStatus(302);
    }
}