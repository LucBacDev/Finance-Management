<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function createUser(array $data);
    // public function findByEmail(string $email);
    public function createToken($user, string $tokenName);
    public function updatePassword($user, string $password);
}
