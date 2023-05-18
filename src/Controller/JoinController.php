<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Repository\TeamRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JoinController
{
    public function __construct(
        private Twig $twig,
        private TeamRepository $teamRepository,
        private Messages $flash

    ) {
        $this->twig = $twig;
    }

    public function handlePost(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        // TODO: handle incorrect teamId's since they can be altered in the HTML
        if (isset($_SESSION['user_id'])) {
            if (isset($data['teamId'])) {
                $this->teamRepository->addMemberToTeam($data['teamId'], $_SESSION['user_id']);
                return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
            } else if (isset($data['teamName'])) {
                $newTeamId = $this->teamRepository->createTeam(new Team(0, $data['teamName'], 0));
                $this->teamRepository->addMemberToTeam($newTeamId, $_SESSION['user_id']);
                return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
            }
        }

        return $response->withHeader('Location', $routeParser->urlFor('join'))->withStatus(302);
    }

    public function showJoin(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $incompleteTeams = [];

        if (isset($_SESSION['user_id'])) {
            $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);

            if ($team) {
                $this->flash->addMessage("notifications", "You are already in a team.");
                return $response->withHeader('Location', '/team-stats')->withStatus(302);
            }

            $incompleteTeams = $this->teamRepository->getIncompleteTeams();
        }

        $notifications = $messages['notifications'] ?? [];

        return $this->twig->render($response,
            'join.twig',
            [
                'formAction' => $routeParser->urlFor("join"),
                'incompleteTeams' => $incompleteTeams,
                'notifs' => $notifications,
            ]);
    }

    public function inviteJoin(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $response->withHeader('Location', $routeParser->urlFor('signUp'))->withStatus(302);
    }

}