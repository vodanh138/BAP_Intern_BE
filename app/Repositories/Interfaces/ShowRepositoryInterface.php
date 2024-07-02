<?php

namespace App\Repositories\Interfaces;

interface ShowRepositoryInterface extends RepositoryInterface
{
    public function getShow();
    public function createShow($template);
}
