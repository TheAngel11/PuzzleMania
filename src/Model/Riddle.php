<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Model;

use JsonSerializable;

class Riddle implements JsonSerializable{

    private string $question;
    private string $answer;

    public static function create(): Riddle
    {
        return new self();
    }

    /**
     * Function called when encoded with json_encode
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
