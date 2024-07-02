<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getAdmin();
    public function createAdmin();
    public function findLoggedUser();
}
