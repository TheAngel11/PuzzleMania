<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Salle\PuzzleMania\Controller\API\RiddlesAPIController;
use Salle\PuzzleMania\Controller\API\UsersAPIController;
use Salle\PuzzleMania\Controller\GameIntroController;
use Salle\PuzzleMania\Controller\GameRiddlesController;
use Salle\PuzzleMania\Controller\JoinController;
use Salle\PuzzleMania\Controller\ProfileController;
use Salle\PuzzleMania\Controller\SignInController;
use Salle\PuzzleMania\Controller\SignUpController;
use Salle\PuzzleMania\Controller\TeamStatsController;
use Salle\PuzzleMania\Middleware\AuthorizationMiddleware;
use Salle\PuzzleMania\Repository\MySQLTeamRepository;
use Salle\PuzzleMania\Repository\MySQLUserRepository;
use Salle\PuzzleMania\Repository\PDOConnectionBuilder;
use Slim\Flash\Messages;
use Slim\Views\Twig;

function addDependencies(ContainerInterface $container): void
{
    $container->set(
        'view',
        function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        }
    );

    $container->set('db', function () {
        $connectionBuilder = new PDOConnectionBuilder();
        return $connectionBuilder->build(
            $_ENV['MYSQL_ROOT_USER'],
            $_ENV['MYSQL_ROOT_PASSWORD'],
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_DATABASE']
        );
    });

    $container->set(
        'flash',
        function () {
            return new Messages();
        }
    );

    $container->set(AuthorizationMiddleware::class, function (ContainerInterface $container) {
        return new AuthorizationMiddleware($container->get('flash'));
    });

    $container->set('user_repository', function (ContainerInterface $container) {
        return new MySQLUserRepository($container->get('db'));
    });

    $container->set('team_repository', function (ContainerInterface $container) {
        return new MySQLTeamRepository($container->get('db'));
    });

    $container->set(
        SignInController::class,
        function (ContainerInterface $c) {
            return new SignInController($c->get('view'), $c->get('user_repository'), $c->get("flash"));
        }
    );

    $container->set(
        SignUpController::class,
        function (ContainerInterface $c) {
            return new SignUpController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        RiddlesAPIController::class,
        function (ContainerInterface $c) {
            return new RiddlesAPIController($c->get('view'));
        }
    );

    $container->set(
        UsersAPIController::class,
        function (ContainerInterface $c) {
            return new UsersAPIController($c->get('view'));
        }
    );

    $container->set(
        GameIntroController::class,
        function (ContainerInterface $c) {
            return new GameIntroController($c->get('view'));
        }
    );

    $container->set(
        ProfileController::class,
        function (ContainerInterface $c) {
            return new ProfileController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        JoinController::class,
        function (ContainerInterface $c) {
            return new JoinController($c->get('view'), $c->get('team_repository'), $c->get("flash"));
        }
    );

    $container->set(
        TeamStatsController::class,
        function (ContainerInterface $c) {
            return new TeamStatsController($c->get('view'), $c->get('team_repository'), $c->get('user_repository'), $c->get("flash"));
        }
    );

    $container->set(
        GameRiddlesController::class,
        function (ContainerInterface $c) {
            return new GameRiddlesController($c->get('view'), $c->get("flash"));
        }
    );
}
