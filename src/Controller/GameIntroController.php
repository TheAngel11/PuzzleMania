<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Repository\MySQLTeamRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GameIntroController
{
    private Twig $twig;
    private int $gameId = 0;
    private MySQLTeamRepository $teamRepository;

    public function __construct(Twig $twig, MySQLTeamRepository $teamRepository) {
        $this->twig = $twig;
        $this->teamRepository = $teamRepository;
    }

    public function showGame(Request $request, Response $response): Response {
        // Check if the user has a team
        $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);

        if ($team == null) {
            return $response->withHeader('Location', '/join')->withStatus(302);
        }

        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('game');


        return $this->twig->render($response, 'game-intro.twig', [
            'groupName' => $team->getName(),
            'formAction' => $formAction
        ]);
    }

    public function gameAction(Request $request, Response $response): Response {
        $this->gameId++;

        return $response->withHeader('Location', "/game/$this->gameId/riddle/1")->withStatus(302);
    }
}