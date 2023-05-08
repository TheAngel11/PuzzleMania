<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Flash\Messages;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TeamStatsController
{
    private Twig $twig;
    private Messages $flash;

    public function __construct(Twig $twig, Messages $flash) {
        $this->twig = $twig;
        $this->flash = $flash;
    }

    public function showTeamStats(Request $request, Response $response): Response {
        return $this->twig->render($response, 'teamStats.twig');

    }

}