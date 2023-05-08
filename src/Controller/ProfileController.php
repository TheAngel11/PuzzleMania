<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ProfileController
{
    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showProfile(Request $request, Response $response): Response {
        $username = '';
        if(isset($_SESSION['user'])) {
            $username = substr($_SESSION['user'], 0, strpos($_SESSION['user'], '@'));
        }

        return $this->twig->render($response, 'profile.twig', [
            'username' => $username
        ]);
    }

}