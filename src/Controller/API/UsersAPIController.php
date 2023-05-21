<?php

namespace Salle\PuzzleMania\Controller\API;

use Slim\Views\Twig;

class UsersAPIController
{
    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

}