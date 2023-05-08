<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\ErrorHandler\HttpErrorHandler;
use Salle\PuzzleMania\Repository\MySQLUserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ProfileController
{
    private Twig $twig;
    private MySQLUserRepository $userRepository;

    public function __construct(Twig $twig, MySQLUserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showProfile(Request $request, Response $response): Response
    {
        $username = '';
        $formData = [];
        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('profile');
        if (isset($_SESSION['user_email'])) {
            $username = substr($_SESSION['user_email'], 0, strpos($_SESSION['user_email'], '@'));
            $formData['email'] = $_SESSION['user_email'];
        }

        return $this->twig->render($response, 'profile.twig', [
            'username' => $username,
            'formAction' => $formAction,
            'formData' => $formData,
        ]);
    }

    public function profileAction(Request $request, Response $response): Response
    {
        if(empty($_FILES['file']['name'])) {
            $this->showProfile($request, $response);
            return $response->withHeader('Location', '/profile')->withStatus(302);
        }

        $errorHandler = new HttpErrorHandler(array());
        $formErrors = $errorHandler->validateProfile();

        if(!empty($formErrors)) {
            $username = '';
            $formData = [];
            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('profile');
            if (isset($_SESSION['user_email'])) {
                $username = substr($_SESSION['user_email'], 0, strpos($_SESSION['user_email'], '@'));
                $formData['email'] = $_SESSION['user_email'];
            }


            return $this->twig->render($response, 'profile.twig', [
                'username' => $username,
                'formAction' => $formAction,
                'formData' => $formData,
                'formErrors' => $formErrors
            ]);
        }


        return $response->withHeader('Location', '/profile')->withStatus(302);



    }

}