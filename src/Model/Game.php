<?php

namespace Salle\PuzzleMania\Model;

class Game
{
    private int $riddles;
    private int $gameId;
    private int $score;

    public function __construct(int $riddles, int $gameId, int $score)
    {
        $this->riddles = $riddles;
        $this->gameId = $gameId;
        $this->score = $score;
    }

    public function getRiddles(): int
    {
        return $this->riddles;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}