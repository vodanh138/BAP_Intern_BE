<?php

namespace App\Repositories\Interfaces;

interface TemplateRepositoryInterface extends RepositoryInterface
{
    public function checkTemplate();
    public function createTemplate($name, $headerType, $footerType, $title, $footer, $avaPath);
    public function getATemplate($id);
    public function getAllTemplate();
    public function getATemplateByName($name);
}
