<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Model\Game;
use Salle\PuzzleMania\Model\Riddle;
use Salle\PuzzleMania\Repository\MySQLGameRepository;
use Salle\PuzzleMania\Repository\MySQLRiddleRepository;
use Salle\PuzzleMania\Repository\MySQLTeamRepository;
use Slim\Flash\Messages;
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

    private Messages $flash;

    public function __construct(Twig $twig, MySQLTeamRepository $teamRepository, MySQLGameRepository $gameRepository,
    MySQLRiddleRepository $riddleRepository, Messages $flash) {
        $this->twig = $twig;
        $this->teamRepository = $teamRepository;
        $this->gameRepository = $gameRepository;
        $this->riddleRepository = $riddleRepository;
        $this->flash = $flash;
    }

    public function showGame(Request $request, Response $response): Response {
        if (isset($_SESSION['user_id'])) {
            $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);

            if ($team == null) {
                $this->flash->addMessage('notifications', 'You must join a team before playing');
                return $response->withHeader('Location', '/join')->withStatus(302);
            }

            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('game');

            $messages = $this->flash->getMessages();
            $notifications = $messages['notifications'] ?? [];

            return $this->twig->render($response, 'game-intro.twig', [
                'groupName' => $team->getName(),
                'formAction' => $formAction,
                'notifs' => $notifications
            ]);

        } else {
            return $response;
        }
    }

    public function gameAction(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        // Generating riddles
        $riddles = $this->riddleRepository->getRandomRiddles();
        //Saving the game in the DB

        if ($riddles == null) {
            $this->flash->addMessage('notifications', 'There are not enough riddles in the database');
            return $response->withHeader('Location', $routeParser->urlFor('game'))->withStatus(302);
        }

        //riddles is an array of Riddle objects
        $game = new Game($riddles[0]->getRiddle(), $riddles[1]->getRiddle(), $riddles[2]->getRiddle(), $_SESSION['user_id'], 10);

        $gameId = $this->gameRepository->createGame($game);

        return $response->withHeader('Location', "/game/$gameId/riddle/1")->withStatus(302);
    }
}