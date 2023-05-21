<?php

namespace Salle\PuzzleMania\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PuzzleMania\Repository\MySQLGameRepository;
use Salle\PuzzleMania\Repository\MySQLRiddleRepository;
use Salle\PuzzleMania\Repository\MySQLTeamRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GameRiddlesController
{

    private Twig $twig;
    private MySQLGameRepository $gameRepository;
    private MySQLRiddleRepository $riddleRepository;
    private MySQLTeamRepository $teamRepository;

    public function __construct(Twig $twig, MySQLGameRepository $gameRepository,
                                MySQLRiddleRepository $riddleRepository, MySQLTeamRepository $teamRepository) {
        $this->twig = $twig;
        $this->gameRepository = $gameRepository;
        $this->riddleRepository = $riddleRepository;
        $this->teamRepository = $teamRepository;
    }

    public function showRiddle(Request $request, Response $response): Response {
        $riddleId = $request->getAttribute('riddleId');
        $gameId = $request->getAttribute('gameId');

        $catImage = $this->getCatImage();

        // Get the riddle
        $riddle = $this->gameRepository->getRiddle($gameId, $riddleId);

        if ($riddle == "")
            return $response->withHeader('Location', '/game')->withStatus(302);


        $formAction = RouteContext::fromRequest($request)->getRouteParser()->urlFor
        ('showRiddle', ['gameId' => $gameId, 'riddleId' => $riddleId]);

        return $this->twig->render($response, 'game-riddles.twig', [
            'formActionSubmit' => $formAction,
            'riddleId' => $riddleId,
            'riddle' => $riddle,
            'catImage' => $catImage
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
            // We sum the score to the team
            $team = $this->teamRepository->getTeamByUserId($_SESSION['user_id']);
            $this->teamRepository->sumTeamScore($team->getId(), $score);

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

    public function getCatImage() {
        $apiKey = 'live_amaf3W0utjlsRFMDkILeWjdoKxECAsx93UZYDFoLUYSUlXpgPiIiA6GjtWnUvamW';
        $apiUrl = "https://api.thecatapi.com/v1/images/search?limit=1&api_key=$apiKey";
        $guzzleClient = new Client();
        $response = null;
        try {
            // Make the request
            $request = $guzzleClient->request('GET', $apiUrl);
            if($request->getStatusCode() == 200){
                $response = json_decode($request->getBody()->getContents());
            }

        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
        return $response[0]->url;
    }
}