<?php

namespace App\Repositories\Interfaces;

interface TemplateRepositoryInterface extends RepositoryInterface
{
    public function checkTemplate();
    public function createTemplate($name, $logo, $title, $footer, $ava_path);
    public function getATemplate($id);
    public function getAllTemplate();
    public function getATemplateByName($name);
}
