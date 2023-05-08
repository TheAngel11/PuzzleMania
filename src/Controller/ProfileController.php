<?php

namespace Salle\PuzzleMania\Controller;

use Slim\Routing\RouteContext;
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
        $formData = [];
        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('signIn');
        if(isset($_SESSION['user_email'])) {
            $username = substr($_SESSION['user_email'], 0, strpos($_SESSION['user_email'], '@'));
            $formData['email'] = $_SESSION['user_email'];
        }

        return $this->twig->render($response, 'profile.twig', [
            'username' => $username,
            'formAction' => $formAction,
            'formData' => $formData,
            'uploads' => "../public/uploads/"
        ]);
    }

}