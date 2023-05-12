<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\User;

interface UserRepository
{
    public function createUser(User $user): int;
    public function getUserByEmail(string $email);
    public function getUserById(int $id);
    public function getAllUsers();
    public function getMembersByTeamId(int $id);
}
