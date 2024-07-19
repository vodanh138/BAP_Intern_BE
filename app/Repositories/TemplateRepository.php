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
    public function createTemplate($name,$header_type,$footer_type, $logo, $title, $footer, $avaPath)
    {
        return $this->model->create(
            [
            'name' => $name,
            'header_type' => 1,
            'footer_type' => 1,
            'logo' => $logo,
            'title' => $title,
            'footer' => $footer,
            'avaPath' => $avaPath,
            ]
        );
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
