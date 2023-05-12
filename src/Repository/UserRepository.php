<?php

declare(strict_types=1);

namespace Salle\PuzzleMania\Repository;

use Salle\PuzzleMania\Model\User;

interface UserRepository
{
    public function createUser(User $user): void;
    public function getUserByEmail(string $email);
    public function getUserById(int $id);
    public function getAllUsers();
    public function getMembersByTeamId(int $id);
    public function setUuidByID(int $id, string $uuid);
    public function getUuidByID(int $id);
}
