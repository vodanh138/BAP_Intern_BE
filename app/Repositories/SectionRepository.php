<?php

namespace App\Repositories;

use App\Models\Section;
use App\Repositories\Interfaces\SectionRepositoryInterface;

class SectionRepository extends BaseRepository implements SectionRepositoryInterface
{
    public function model(): string
    {
        return Section::class;
    }
    public function selectSectionBelongTo($template_id)
    {
        return $this->model->where('template_id', $template_id);
    }
    public function createSection($type, $title, $content1, $content2, $bgColor, $textColor, $template_id)
    {
        return $this->model->create(
            [
            'type' => $type,
            'title' => $title,
            'content1' => $content1,
            'content2' => $content2,
            'bgColor' => $bgColor,
            'textColor' => $textColor,
            'template_id' => $template_id,
            ]
        );
    }
}
