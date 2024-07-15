<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function model(): string
    {
        return User::class;
    }

    public function getAdmin()
    {
        return $this->model->where('username', 'test01')->first();
    }
    public function createAdmin()
    {
        return $this->model->create(
            [
            'username' => 'test01',
            'password' => bcrypt('123456'),
            ]
        );
    }
    public function findLoggedUser()
    {
        return $this->model->find(Auth::id());
    }
}
