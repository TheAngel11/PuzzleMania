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
            $formData['picture'] = $this->userRepository->getUuidByID($_SESSION['user_id']);
        }

        if (!is_dir(__DIR__ . '/../../public/uploads')) {
            mkdir(__DIR__ . '/../../public/uploads', 0777, true);
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

        if(empty($formErrors)) {
            // There are no errors
            $filename = $_FILES['file']['name'];
            $mime_type = mime_content_type($_FILES['file']['tmp_name']);

            $fileExtension = substr($mime_type, strpos($mime_type, '/') + 1);
            $uuid = uniqid() . '.' . $fileExtension;;
            $targetFile = __DIR__ . '/../../public/uploads/' . $uuid;

            // Saving img in the server
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);

            // Saving uuid in the database
            $this->userRepository->setUuidByID($_SESSION['user_id'], $uuid);
        }

        $username = '';
        $formData = [];
        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor('profile');
        if (isset($_SESSION['user_email'])) {
            $username = substr($_SESSION['user_email'], 0, strpos($_SESSION['user_email'], '@'));
            $formData['email'] = $_SESSION['user_email'];
            $formData['picture'] = $this->userRepository->getUuidByID($_SESSION['user_id']);
        }


        return $this->twig->render($response, 'profile.twig', [
            'username' => $username,
            'formAction' => $formAction,
            'formData' => $formData,
            'formErrors' => $formErrors

        ]);
    }

}