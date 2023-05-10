<?php

declare(strict_types=1);

use DI\Container;
use Salle\PuzzleMania\Controller\API\RiddlesAPIController;
use Salle\PuzzleMania\Controller\GameIntroController;
use Salle\PuzzleMania\Controller\GameRiddlesController;
use Salle\PuzzleMania\Controller\JoinController;
use Salle\PuzzleMania\Controller\ProfileController;
use Salle\PuzzleMania\Controller\SignInController;
use Salle\PuzzleMania\Controller\SignUpController;
use Salle\PuzzleMania\Controller\TeamStatsController;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

function addRoutes(App $app, Container $container): void
{
    $app->get('/', SignInController::class . ':showHome')->setName('showHome');
    $app->get('/sign-in', SignInController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', SignInController::class . ':signIn');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');

    $app->group('', function (RouteCollectorProxy  $group) {
        $group->get('/profile', ProfileController::class . ':showProfile')->setName('profile');
        $group->get('/join', JoinController::class . ':showJoin')->setName('join');
        $group->get('/team-stats', TeamStatsController::class . ':showJoin')->setName('teamStats');
        $group->get('/riddles', RiddlesAPIController::class . ':showRiddles')->setName('riddles');
        $group->get('/game', GameIntroController::class . ':showGame')->setName('game');
    })->add(AuthorizationMiddleware::class);

    $app->post('/profile', ProfileController::class . ':profileAction')->setName('profileAction');
    $app->post('/game', GameIntroController::class . ':gameAction')->setName('gameAction');

    // make a route /game/{gameId}/riddle/{riddleId}:
    $app->get('/game/{gameId}/riddle/{riddleId}', GameRiddlesController::class . ':showRiddle')->setName('showRiddle');


    //TODO: Falten posts
    //TODO: Falten les rutes amb ID
}
