<?php

namespace App\Services\Interfaces;

interface TemplateServiceInterface
{
    public function loginProcessing($username,$password);
    public function addTemplate($request);
    public function editTemplate($request, $template);
    public function deleteTemplate($template);
    public function show();
    public function getTemplate($template);
    public function cloneTemplate($template);
    public function getAllTemplates();
    public function changeTemplate($template);
    public function addSection($template_id);
    public function deleteSection($section);
    public function editSection($request, $Section);
    
}