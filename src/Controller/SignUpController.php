<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Controller;

use http\Message;
use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Service\ValidatorService;
use Salle\PuzzleMania\Repository\UserRepository;
use Salle\PuzzleMania\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

use DateTime;

final class SignUpController
{
    private ValidatorService $validator;

    public function __construct(
        private Twig $twig,
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private Messages $flash
    )
    {
        $this->validator = new ValidatorService();
    }

    /**
     * Renders the form
     */
    public function showSignUpForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render(
            $response,
            'sign-up.twig',
            [
                'formAction' => $routeParser->urlFor('signUp')
            ]
        );
    }

    public function signUp(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $errors = [];

        $errors['email'] = $this->validator->validateEmail($data['email']);
        $errors['password'] = $this->validator->validatePassword($data['password']);
        //$errors['birthday'] = $this->validator->validateBirthday($data['birthday']);
        if ($data['password'] != $data['repeatPassword']) {
            $errors['password'] = "Passwords do not match.";
        }

        // Unset variables if there are no errors
        if ($errors['email'] == '') {
            unset($errors['email']);
        }
        if ($errors['password'] == '') {
            unset($errors['password']);
        }

        $savedUser = $this->userRepository->getUserByEmail($data['email']);
        if ($savedUser != null) {
            $errors['email'] = "User already exists!";
        }
        if (count($errors) == 0) {
            $user = User::create()
                ->setEmail($data['email'])
                ->setPassword(md5($data['password']))
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());
            $user->setId($this->userRepository->createUser($user));


            if (isset($_SESSION['team_id_invite'])) {
                if ($this->teamRepository->getTeamNumberOfMembers(intval($_SESSION['team_id_invite'])) >= 2) {
                    $this->flash->addMessage('notifications', 'Team is full');
                    unset($_SESSION['team_id_invite']);
                    return $response->withHeader('Location', $routeParser->urlFor('signIn'))->withStatus(302);
                }
                $this->teamRepository->addMemberToTeam(intval($_SESSION['team_id_invite']), $user->getId());
                unset($_SESSION['team_id_invite']);
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_email'] = $user->email();
                return $response->withHeader('Location', $routeParser->urlFor('teamStats'))->withStatus(302);
            }


            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
        return $this->twig->render(
            $response,
            'sign-up.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('signUp')
            ]
        );
    }
}
