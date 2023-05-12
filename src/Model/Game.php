<?php

namespace Salle\PuzzleMania\Model;

class Game
{
    private string $riddle1;
    private string $riddle2;
    private string $riddle3;
    private int $userId;
    private int $score;

    public function __construct(string $riddle1, string $riddle2, string $riddle3, int $userId, int $score)
    {
        $this->riddle1 = $riddle1;
        $this->riddle2 = $riddle2;
        $this->riddle3 = $riddle3;
        $this->userId = $userId;
        $this->score = $score;
    }

    public function getRiddle1(): string
    {
        return $this->riddle1;
    }

    public function getRiddle2(): string
    {
        return $this->riddle2;
    }

    public function getRiddle3(): string
    {
        return $this->riddle3;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}