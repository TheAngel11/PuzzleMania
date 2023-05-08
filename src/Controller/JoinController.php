<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JoinController
{
    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showJoin(Request $request, Response $response): Response {
        return $this->twig->render($response, 'join.twig');

    }

}