<?php

namespace App\Repositories;

use App\Models\Section;
use App\Models\Template;
use App\Models\Show;
use App\Repositories\Interfaces\TemplateRepositoryInterface;


class TemplateRepository implements TemplateRepositoryInterface
{
    public function createTemplate($name,$logo,$title,$footer)
    {
        return Template::create([
            'name' => $name,
            'logo' => $logo,
            'title' => $title,
            'footer' => $footer,
        ]);
    }
    public function getAllTemplate()
    {
        return Template::all();
    }
    public function getChosenTemplate()
    {
        $show = Show::first();
        if ($show) {
            return Template::find($show->template_id);
        }
        return null;
    }
    public function getShow()
    {
        return Show::first();
    }
    public function selectSectionBelongTo($template_id)
    {
        return Section::where('template_id', $template_id);
    }
    public function createSection($type,$title,$content1,$content2,$template_id)
    {
        return Section::create([
            'type' => $type,
            'title' => $title,
            'content1' => $content1,
            'content2' => $content2,
            'template_id' => $template_id,
        ]);
    }
}
