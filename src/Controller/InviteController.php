<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Model\Team;
use Salle\PuzzleMania\Repository\TeamRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class InviteController
{
    public function __construct(
        private TeamRepository $teamRepository,
        private Messages $flash,
    ) {
    }


    public function inviteJoin(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (isset($_SESSION['user_id'])) {
            $team = $this->teamRepository->getTeamByUserId(intval($_SESSION['user_id']));
            if ($team != null) {
                $this->flash->addMessage('notifications', 'You are already in a team.');
                return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
            }

            $team = $this->teamRepository->getTeamById(intval($request->getAttribute('teamId')) ?? 0);
            if ($team == null) {
                $this->flash->addMessage('notifications', 'Team does not exist');
                return $response->withHeader('Location', $routeParser->urlFor('showHome'))->withStatus(302);
            }

            if ($this->teamRepository->getTeamNumberOfMembers($team->getId()) >= 2) {
                $this->flash->addMessage('notifications', 'Team is full');
                return $response->withHeader('Location', $routeParser->urlFor('showHome'))->withStatus(302);
            }

            $this->teamRepository->addMemberToTeam($team->getId(), intval($_SESSION['user_id']));
            return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
        }

        $_SESSION['team_id_invite'] = intval($request->getAttribute('teamId'));

        return $response->withHeader('Location', $routeParser->urlFor('signUp'))->withStatus(302);
    }

}