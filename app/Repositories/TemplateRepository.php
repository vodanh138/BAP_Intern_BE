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
    public function createTemplate(
        $name,
        $headerType,
        $footerType,
        $title,
        $headerBgColor,
        $headerTextColor,
        $footer,
        $footerBgColor,
        $footerTextColor,
        $avaPath
    ) {
        return $this->model->create(
            [
                'name' => $name,
                'headerType' => 1,
                'footerType' => 1,
                'title' => $title,
                'headerBgColor' => $headerBgColor,
                'headerTextColor' => $headerTextColor,
                'footer' => $footer,
                'footerBgColor' => $footerBgColor,
                'footerTextColor' => $footerTextColor,
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
