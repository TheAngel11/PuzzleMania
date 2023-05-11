<?php

namespace Salle\PuzzleMania\Controller;

use Salle\PuzzleMania\Repository\TeamRepository;
use Salle\PuzzleMania\Repository\UserRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
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
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $hasQR = false;

        if (isset($_SESSION['user_id'])) {
            $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);
            if ($team) {
                $teamMembers = $this->userRepository->getMembersByTeamId($team->getId());

                $teamId = $team->getId();

                if (sizeof($teamMembers) < 2) {
                    $hasQR = true;

                    $data = array(
                        'symbology' => 'QRCode',
                        'code' => 'http://localhost:8030/invite/join/' . $team->getId(),
                        'width' => 512,
                        'height' => 512,
                    );

                    $options = array(
                        'http' => array(
                            'method' => 'POST',
                            'content' => json_encode($data),
                            'header' => "Content-Type: application/json\r\n" .
                                "Accept: image/png\r\n"
                        )
                    );

                    $context = stream_context_create($options);
                    $url = 'http://pw_barcode/BarcodeGenerator';
                    $tmpResponse = file_get_contents($url, false, $context);
                    file_put_contents("assets/qr/$teamId.png", $tmpResponse);
                }
            } else {
                $this->flash->addMessage("notifications", "You are not in a team.");
                return $response->withHeader('Location', $routeParser->urlFor('join'))->withStatus(302);
            }
        }

        $notifications = $messages['notifications'] ?? [];

        return $this->twig->render($response,
            'teamStats.twig',
            [
                'team' => $team,
                'teamMembers' => $teamMembers,
                'hasQR' => $hasQR,
                'notifs' => $notifications,
            ]);

    }

}