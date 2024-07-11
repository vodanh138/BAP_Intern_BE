<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Validator;
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

    public function LoginProcessing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->responseFail($validator->errors(),422);
        }
        return $this->templateService->loginProcessing($request->username, $request->password);
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->responseSuccess([],'Logged out successfully');
        } catch (\Exception $e) {
            return $this->responseFail($e->getMessage(), 500);
        }
    }

    public function AddTemplate(Request $request)
    {
        return $this->templateService->addTemplate($request);
    }

    public function EditTemplate(Request $request, Template $template)
    {
        return $this->templateService->editTemplate($request, $template);
    }

    public function DeleteTemplate(Request $request)
    {
        $templateIds = $request->input('template_ids', []);
        return $this->templateService->deleteTemplate($templateIds);
    }

    public function Show()
    {
        return $this->templateService->show();
    }
    public function CloneTemplate(Template $template, Request $request)
    {
        return $this->templateService->cloneTemplate($template, $request);
    }

    public function GetTemplate(Template $template)
    {
        return $this->templateService->getTemplate($template);
    }

    public function GetAllTemplate()
    {
        return $this->templateService->getAllTemplates();
    }

    public function ChangeTemplate(Template $template)
    {
        return $this->templateService->changeTemplate($template);
    }
    public function AddSection(Template $template)
    {
        return $this->templateService->addSection($template->id);
    }
    public function DeleteSection(Section $section)
    {
        return $this->templateService->deleteSection($section);
    }
    public function EditSection(Request $request, Template $template, Section $section)
    {
        return $this->templateService->editSection($request, $section);
    }

    public function EditHeader(Request $request, $templateId)
    {
        return $this->templateService->editHeader($request, $templateId);
    }

    public function EditFooter(Request $request, $templateId)
    {
        return $this->templateService->editFooter($request, $templateId);
    }
    public function EditAvatar(Template $template, Request $request)
    {
        return $this->templateService->editAvatar($request, $template);
    }
}
