<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TeamStatsController
{
    private Twig $twig;
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private Messages $flash;

    public function __construct(Twig $twig, TeamRepository $teamRepository, UserRepository $userRepository, Messages $flash) {
        $this->twig = $twig;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->flash = $flash;
    }

    public function showTeamStats(Request $request, Response $response): Response {
        $teamMembers = null;
        $team = null;

        if (isset($_SESSION['user_id'])) {
            $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);
            if ($team) {
                $teamMembers = $this->userRepository->getMembersByTeamId($team->getId());
            }
        }
        return $this->twig->render($response,
            'teamStats.twig',
            [
                'team' => $team,
                'teamMembers' => $teamMembers
            ]);

    }

}