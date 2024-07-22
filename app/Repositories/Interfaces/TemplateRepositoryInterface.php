<?php

namespace App\Repositories\Interfaces;

interface TemplateRepositoryInterface extends RepositoryInterface
{
    public function checkTemplate();
    public function createTemplate(
        $name,
        $headerType,
        $footerType,
        $title1,
        $title2,
        $headerBgColor,
        $headerTextColor,
        $footer1,
        $footer2,
        $footerBgColor,
        $footerTextColor,
        $avaPath
    );
    public function getATemplate($id);
    public function getAllTemplate();
    public function getATemplateByName($name);
}
