<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Repository\MySQLGameRepository;
use Salle\PuzzleMania\Repository\MySQLRiddleRepository;
use Salle\PuzzleMania\Repository\MySQLTeamRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GameIntroController
{
    private Twig $twig;
    private MySQLTeamRepository $teamRepository;
    private MySQLGameRepository $gameRepository;
    private MySQLRiddleRepository $riddleRepository;

    public function __construct(Twig $twig, MySQLTeamRepository $teamRepository, MySQLGameRepository $gameRepository,
    MySQLRiddleRepository $riddleRepository) {
        $this->twig = $twig;
        $this->teamRepository = $teamRepository;
        $this->gameRepository = $gameRepository;
        $this->riddleRepository = $riddleRepository;
    }

    public function showGame(Request $request, Response $response): Response {
        if (isset($_SESSION['user_id'])) {
            $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);

            if ($team == null) {
                return $response->withHeader('Location', '/join')->withStatus(302);
            }

            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('game');


            return $this->twig->render($response, 'game-intro.twig', [
                'groupName' => $team->getName(),
                'formAction' => $formAction
            ]);

        } else {
            return $response;
        }
    }

    public function gameAction(Request $request, Response $response): Response {

        // Generating riddles
        $riddles = $this->riddleRepository->getRandomRiddles();
        //Saving the game in the DB
        $game = new Game($riddles[0], $riddles[1], $riddles[2], $_SESSION['user_id'], 10);
        $gameId = $this->gameRepository->createGame($game);

        return $response->withHeader('Location', "/game/$gameId/riddle/1")->withStatus(302);
    }
}