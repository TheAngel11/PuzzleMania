<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Model;

use JsonSerializable;

class Riddle implements JsonSerializable{

    private int $id;
    private int $userId;
    private string $riddle;
    private string $answer;

    public static function create(): Riddle
    {
        return new self();
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRiddle(): string
    {
        return $this->riddle;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setRiddle(string $riddle): void
    {
        $this->riddle = $riddle;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

}