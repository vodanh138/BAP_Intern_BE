<?php

namespace App\Services\Interfaces;

interface TemplateServiceInterface
{
    public function loginProcessing($username, $password);
    public function addTemplate($request);
    public function deleteTemplate($template);
    public function show();
    public function getTemplate($template);
    public function cloneTemplate($template, $request);
    public function getAllTemplates();
    public function changeTemplate($template);
    public function addSection($template_id);
    public function deleteSection($section);
    public function editSection($request, $Section);
    public function editHeader($request, $templateId);
    public function editFooter($request, $templateId);
    public function editAvatar($request, $template);
    public function loginProcessingLocale($username, $password, $locale);
}
