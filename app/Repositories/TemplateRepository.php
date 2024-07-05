<?php

namespace App\Repositories;
use App\Models\Template;
use App\Repositories\Interfaces\TemplateRepositoryInterface;


class TemplateRepository extends BaseRepository implements TemplateRepositoryInterface
{
    public function model(): string
    {
        return Template::class;
    }
    public function checkTemplate()
    {
        return $this->model->first();
    }
    public function createTemplate($name, $logo, $title, $footer)
    {
        return $this->model->create([
            'name' => $name,
            'logo' => $logo,
            'title' => $title,
            'footer' => $footer,
        ]);
    }
    public function getAllTemplate()
    {
        return $this->model->all();
    }
    public function getATemplate($id)
    {
        return $this->model->find($id);
    }
}
