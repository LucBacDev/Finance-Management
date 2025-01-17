<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\Hash;
use App\Repositories\BaseRepository;
use Laravel\Sanctum\PersonalAccessToken;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\User::class;
    }
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->model->create($data);
    }

    public function createToken($user, string $tokenName)
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    public function updatePassword($user, string $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
