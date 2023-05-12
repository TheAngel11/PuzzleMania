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
    ) {
    }


    public function inviteJoin(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (isset($_SESSION['user_id'])) {
            $this->teamRepository->addMemberToTeam($_SESSION['team']->getId(), intval($_SESSION['user_id']));
            return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
        }

        $_SESSION['team_id_invite'] = $request->getAttribute('teamId');

        return $response->withHeader('Location', $routeParser->urlFor('signUp'))->withStatus(302);
    }

}