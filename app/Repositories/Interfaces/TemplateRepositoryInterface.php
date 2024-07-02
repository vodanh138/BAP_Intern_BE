<?php

namespace App\Repositories\Interfaces;

interface TemplateRepositoryInterface
{
    public function createTemplate($name,$logo,$title,$footer);
    public function getChosenTemplate();
    public function getShow();
    public function selectSectionBelongTo($template_id);
    public function getAllTemplate();
    public function createSection($type,$title,$content1,$content2,$template_id);

}
