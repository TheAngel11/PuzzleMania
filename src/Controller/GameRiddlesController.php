<?php

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Repository\MySQLGameRepository;
use Salle\PuzzleMania\Repository\MySQLRiddleRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GameRiddlesController
{

    private Twig $twig;
    private MySQLGameRepository $gameRepository;
    private MySQLRiddleRepository $riddleRepository;

    public function __construct(Twig $twig, MySQLGameRepository $gameRepository, MySQLRiddleRepository $riddleRepository) {
        $this->twig = $twig;
        $this->gameRepository = $gameRepository;
        $this->riddleRepository = $riddleRepository;
    }

    public function showRiddle(Request $request, Response $response): Response {
        $riddleId = $request->getAttribute('riddleId');
        $gameId = $request->getAttribute('gameId');

        // Get the riddle
        $riddle = $this->gameRepository->getRiddle($gameId, $riddleId);

        if ($riddle == "")
            return $response->withHeader('Location', '/game')->withStatus(302);


        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
        ('showRiddle', ['gameId' => $gameId, 'riddleId' => $riddleId]);

        return $this->twig->render($response, 'game-riddles.twig', [
            'formActionSubmit' => $formAction,
            'riddleId' => $riddleId,
            'riddle' => $riddle
        ]);
    }

    public function riddleAction(Request $request, Response $response): Response {
        $riddleId = $request->getAttribute('riddleId') + 1;
        $gameId = $request->getAttribute('gameId');

        // Get the riddle
        $riddle = $this->gameRepository->getRiddle($gameId, $riddleId - 1);

        if ($riddle == "")
            return $response->withHeader('Location', '/game')->withStatus(302);

        // Get the riddle answer
        $answer = $this->riddleRepository->getAnswerByQuestion($riddle);

        // Get the answer from the form
        $data = ($request->getParsedBody())['riddle'];
        $data = strtolower($data);
        $answer = strtolower($answer);

        $score = $this->gameRepository->getScore($gameId);

        if ($data == $answer) {
            $correct = true;
            $score += 10;
        } else {
            $correct = false;
            $score -= 10;
        }

        $this->gameRepository->updateScore($gameId, $score);

        if($riddleId > 3 || $score <= 0) {
            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
            ('teamStats');
            $finish = true;
        } else {
            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
            ('showRiddle', ['gameId' => $gameId, 'riddleId' => $riddleId]);
            $finish = false;
        }

        return $this->twig->render($response, 'game-riddles.twig', [
            'riddleId' => $riddleId - 1,
            'riddle' => $riddle,
            'correct' => $correct,
            'finish' => $finish,
            'answer' => $answer,
            'formAction' => $formAction,
            'score' => $score
        ]);
    }
}