<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Routing\RouteContext;
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
        // TODO: Check if the user has joined a team

        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('game');


        return $this->twig->render($response, 'game.twig', [
            'groupName' => "HARDCODED",
            'formAction' => $formAction
        ]);
    }

    public function gameAction(Request $request, Response $response): Response {
        //TODO: Do the game action
        return $response->withHeader('Location', '/game')->withStatus(302);
    }

}