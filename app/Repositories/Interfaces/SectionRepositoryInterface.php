<?php

namespace App\Repositories\Interfaces;

interface SectionRepositoryInterface extends RepositoryInterface
{
    public function selectSectionBelongTo($template_id);
    public function createSection($type, $title, $content1, $content2, $template_id);
}
