<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Repository\TeamRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

const MAX_TEAM_NAME_LENGTH = 255;

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

        // Check if the team id exists in the database:
        if (isset($data['teamId'])) {
            $team = $this->teamRepository->getTeamById(intval($data['teamId']));
            if ($team == null) {
                $this->flash->addMessage("notifications", "The team you are trying to join does not exist.");
                return $response->withHeader('Location', $routeParser->urlFor('join'))->withStatus(302);
            }
        }
        // The team id is correct, so we can add the user to the team:
        if (isset($_SESSION['user_id'])) {
            if (isset($data['teamId'])) {
                if ($this->teamRepository->getTeamNumberOfMembers(intval($data['teamId'])) >= 2) {
                    $this->flash->addMessage('notifications', 'Team is full');
                    return $response->withHeader('Location', $routeParser->urlFor('join'))->withStatus(302);
                }

                $this->teamRepository->addMemberToTeam($data['teamId'], $_SESSION['user_id']);
                return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
            } else if (isset($data['teamName'])) {
                // Check that the name of the team has an appropriate length
                // No more than the limit of chars established by the database (VARCHAR (255))
                if (strlen($data['teamName']) > MAX_TEAM_NAME_LENGTH) {
                    $this->flash->addMessage("notifications", "The team name is too long.");
                    return $response->withHeader('Location', $routeParser->urlFor('join'))->withStatus(302);
                }
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

        $messages = $this->flash->getMessages();

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