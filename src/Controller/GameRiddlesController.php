<?php

namespace Salle\PuzzleMania\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GameRiddlesController
{

    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showRiddle(Request $request, Response $response): Response {
        $riddleId = $request->getAttribute('riddleId');
        $gameId = $request->getAttribute('gameId');
        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
        ('showRiddle', ['gameId' => $gameId, 'riddleId' => $riddleId]);

        return $this->twig->render($response, 'game-riddles.twig', [
            'formActionSubmit' => $formAction,
            'riddleId' => $riddleId,
            'riddle' => "Riddle hardcoded lorem ipsum foo foobar etc."
        ]);
    }

    public function riddleAction(Request $request, Response $response): Response {
        $riddleId = $request->getAttribute('riddleId') + 1;
        $gameId = $request->getAttribute('gameId');

        if($riddleId > 3) {
            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
            ('showHome');
        } else {
            $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
            ('showRiddle', ['gameId' => $gameId, 'riddleId' => $riddleId]);
        }

        return $this->twig->render($response, 'game-riddles.twig', [
            'riddleId' => $riddleId - 1,
            'riddle' => "Riddle hardcoded lorem ipsum foo foobar etc.",
            'correct' => false, //TODO: check if answer is correct
            'answer' => "Answer hardcoded lorem ipsum foo foobar etc.",
            'formAction' => $formAction
        ]);
    }
}