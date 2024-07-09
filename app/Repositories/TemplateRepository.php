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
    public function createTemplate($name, $logo, $title, $footer,$ava_path)
    {
        return $this->model->create([
            'name' => $name,
            'logo' => $logo,
            'title' => $title,
            'footer' => $footer,
            'ava_path' => $ava_path,
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
    public function getATemplateByName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}
