<?php

namespace App\Repositories;

use App\Models\Show;
use App\Repositories\Interfaces\ShowRepositoryInterface;

class ShowRepository extends BaseRepository implements ShowRepositoryInterface
{
    public function model(): string
    {
        return Show::class;
    }
    public function getShow()
    {
        return $this->model->first();
    }
    public function createShow($template)
    {
        return $this->model->create(
            [
            'template_id' => $template->id,
            ]
        );
        ;
    }
}
