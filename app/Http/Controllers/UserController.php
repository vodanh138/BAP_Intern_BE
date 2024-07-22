<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarRequest;
use App\Http\Requests\FooterRequest;
use App\Http\Requests\HeaderRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SectionRequest;
use App\Http\Requests\TemplateRequest;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Section;
use App\Services\Interfaces\TemplateServiceInterface;
use App\Traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    protected $templateService;
    public function __construct(TemplateServiceInterface $templateService)
    {
        $this->templateService = $templateService;
    }

    public function loginProcessing(LoginRequest $request)
    {
        return $this->templateService->loginProcessing($request->username, $request->password);
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->responseSuccess([], __('messages.logout-T'));
        } catch (\Exception $e) {
            return $this->responseFail($e->getMessage(), 500);
        }
    }

    public function addTemplate(TemplateRequest $request)
    {
        return $this->templateService->addTemplate($request);
    }

    public function deleteTemplate(Request $request)
    {
        $templateIds = $request->input('templateId', []);
        return $this->templateService->deleteTemplate($templateIds);
    }

    public function show()
    {
        return $this->templateService->show();
    }
    public function cloneTemplate(Template $template, TemplateRequest $request)
    {
        return $this->templateService->cloneTemplate($template, $request);
    }

    public function getTemplate(Template $template)
    {
        return $this->templateService->getTemplate($template);
    }

    public function getAllTemplate()
    {
        return $this->templateService->getAllTemplates();
    }

    public function changeTemplate(Template $template)
    {
        return $this->templateService->changeTemplate($template);
    }
    public function addSection(Template $template)
    {
        return $this->templateService->addSection($template->id);
    }
    public function deleteSection(Section $section)
    {
        return $this->templateService->deleteSection($section);
    }
    public function editSection(SectionRequest $request, Template $template, Section $section)
    {
        if (!$template) {
            return $this->responseFail(__('messages.template') . $template->id . __('messages.notFound'), 404);
        }
        return $this->templateService->editSection($request, $section);
    }

    public function editHeader(HeaderRequest $request, $templateId)
    {
        return $this->templateService->editHeader($request, $templateId);
    }

    public function editFooter(FooterRequest $request, $templateId)
    {
        return $this->templateService->editFooter($request, $templateId);
    }
    public function editAvatar(Template $template, AvatarRequest $request)
    {
        return $this->templateService->editAvatar($request, $template);
    }
    public function loginProcessingLocale(LoginRequest $request, $locale)
    {
        return $this->templateService->loginProcessingLocale($request->username, $request->password, $locale);
    }
}
