<?php
namespace App\Services;

use App\Services\Interfaces\TemplateServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\TemplateRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ShowRepositoryInterface;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class TemplateService implements TemplateServiceInterface
{
    use ApiResponse;
    protected $templateRepository;
    protected $userRepository;
    protected $showRepository;
    protected $roleRepository;
    protected $sectionRepository;

    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        UserRepositoryInterface $userRepository,
        showRepositoryInterface $showRepository,
        sectionRepositoryInterface $sectionRepository,
        roleRepositoryInterface $roleRepository
    ) {
        $this->templateRepository = $templateRepository;
        $this->userRepository = $userRepository;
        $this->showRepository = $showRepository;
        $this->sectionRepository = $sectionRepository;
        $this->roleRepository = $roleRepository;
    }
    public function loginProcessing($username, $password)
    {
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = $this->userRepository->findLoggedUser();
            try {
                $token = $user->createToken('auth_token')->plainTextToken;
            } catch (\Exception $e) {
                return $this->responseFail($e->getMessage());
            }
            return $this->responseSuccess(
                [
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'username' => $user->username,
                'role' => $user->hasRole('admin') ? 'ADMIN' : 'USER',
                ],
                'Log in successfully'
            );
        } else {
            return $this->responseFail('Username or password incorect');
        }
    }
    public function addTemplate($request)
    {
        $template = $this->templateRepository->getATemplateByName($request->name);
        if ($template) {
            return $this->responseFail("Template's name must be unique");
        }
        DB::beginTransaction();

        try {
            $template = $this->templateRepository->createTemplate($request->name, 'lg', 'default-title', 'default-footer', '/images/default-ava.png');
            if (!$template) {
                return $this->responseFail('Failed to create template in database');
            }
            if (!$this->addSection($template->id)) {
                return $this->responseFail('Failed to create section in database');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseFail('Some error orcurr when adding template', 500);
        }
        return $this->responseSuccess(
            [
            'template' => $template,
            ],
            'Create template successfully'
        );
    }

    public function editTemplate($request, $template)
    {
        $template->update(
            [
            'name' => $request->name,
            ]
        );
        return $this->responseSuccess(
            [
            'template' => $template,
            ],
            "Edit template's name successfully"
        );
    }

    public function deleteTemplate($templateIds)
    {
        if (is_string($templateIds)) {
            $templateIds = explode(',', $templateIds);
        }
        if (!is_array($templateIds)) {
            return $this->responseFail('Invalid template_ids format.', 400);
        }
        $show = $this->showRepository->getShow();
        foreach ($templateIds as $templateId) {
            if (!$this->templateRepository->getATemplate($templateId)) {
                return $this->responseFail('Template ' . $templateId . ' not found');
            }
            if ($templateId == $show->template_id) {
                return $this->responseFail('Cannot delete template ' . $templateId . '(chosen template)');
            }
        }
        $template = '';
        foreach ($templateIds as $templateId) {
            $this->templateRepository->getATemplate($templateId)->delete();
            $template .= $templateId . ',';
        }
        $template = rtrim($template, ',');
        return $this->responseSuccess([], 'Template ' . $template . ' deleted successfully');
    }

    public function show()
    {
        $show = $this->showRepository->getShow();
        if (!$show) {
            return $this->responseFail('There is nothing to show');
        }
        $chosenTemplate = $this->templateRepository->getATemplate($show->template_id);
        if (!$chosenTemplate) {
            return $this->responseFail('No template have been chosen');
        }
        $query = $this->sectionRepository->selectSectionBelongTo($chosenTemplate->id)->get();

        return $this->responseSuccess(
            [
            'id' => $chosenTemplate->id,
            'logo' => $chosenTemplate->logo,
            'title' => $chosenTemplate->title,
            'footer' => $chosenTemplate->footer,
            'avaPath' => $chosenTemplate->avaPath,
            'section' => $query,
            ],
            'Show successfully'
        );
    }

    public function getTemplate($template)
    {
        $query = $this->sectionRepository->selectSectionBelongTo($template->id)->get();
        return $this->responseSuccess(
            [
            'id' => $template->id,
            'logo' => $template->logo,
            'title' => $template->title,
            'footer' => $template->footer,
            'avaPath' => $template->avaPath,
            'section' => $query,
            ]
        );
    }
    public function cloneTemplate($template, $request)
    {
        $template1 = $this->templateRepository->getATemplateByName($request->name);
        if ($template1) {
            return $this->responseFail("Template's name must be unique");
        }
        DB::beginTransaction();

        try {
            $newtemplate = $this->templateRepository->createTemplate($request->name, $template->logo, $template->title, $template->footer, $template->avaPath);
            if (!$newtemplate) {
                return $this->responseFail('Failed to create template in database');
            }
            try {
                $this->sectionRepository->selectSectionBelongTo($template->id)->get()->map(
                    function ($section) use ($newtemplate) {
                        $this->sectionRepository->createSection($section->type, $section->title, $section->content1, $section->content2, $newtemplate->id);
                    }
                );
            } catch (\Exception $e) {
                return $this->responseFail($e->getMessage(), 500);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseFail('Some error orcurr when copying template', 500);
        }

        return $this->responseSuccess(
            [
            'template' => $this->getTemplate($newtemplate)->original,
            ],
            'Clone template successfully'
        );
    }

    public function getAllTemplates()
    {
        $user = Auth::user();
        $show = $this->showRepository->getShow();
        return $this->responseSuccess(
            [
            'username' => $user->username,
            'chosen' => $show->template_id,
            'templates' => $this->templateRepository->getAllTemplate(),
            ]
        );
    }

    public function changeTemplate($template)
    {
        $show = $this->showRepository->getShow();
        $show->update(
            [
            'template_id' => $template->id,
            ]
        );
        return $this->getTemplate($template);
    }
    public function addSection($template_id)
    {
        $section = $this->sectionRepository->createSection(1, 'default-title', 'default-content1', '', $template_id);
        if (!$section) {
            return $this->responseFail('Failed to create section in database');
        }
        return $this->responseSuccess(
            [
            'section' => $section,
            ],
            'Add section successfully'
        );
    }
    public function deleteSection($section)
    {
        $count = $this->sectionRepository->selectSectionBelongTo($section->template_id)->count();
        if ($count === 1) {
            return $this->responseFail('Cannot delete the only section');
        }

        $section->delete();

        return $this->responseSuccess([], 'Section deleted successfully');
    }
    public function editSection($request, $Section)
    {
        $Section->update(
            [
            'type' => $request->type,
            'title' => $request->title,
            'content1' => $request->input('content1', ''),
            ]
        );
        if ($request->type == 2) {
            $Section->update(
                [
                'content2' => $request->input('content2', ''),
                ]
            );
        } else {
            $Section->update(
                [
                'content2' => '',
                ]
            );
        }
        return $this->responseSuccess(
            [
            'section' => $Section,
            ],
            'Edit template successfully'
        );
    }

    public function editHeader($request, $templateId)
    {
        $template = $this->templateRepository->getATemplate($templateId);

        if (!$template) {
            return $this->responseFail('Template not found', 404);
        }
        $template->update(
            [
            'title' => $request->title,
            ]
        );
        return $this->responseSuccess(
            [
            'template' => $template,
            ],
            'Header updated successfully'
        );
    }

    public function editFooter($request, $templateId)
    {
        $template = $this->templateRepository->getATemplate($templateId);

        if (!$template) {
            return $this->responseFail('Template not found', 404);
        }

        $template->update(
            [
            'footer' => $request->footer,
            ]
        );

        return $this->responseSuccess(
            [
            'template' => $template,
            ],
            'Footer updated successfully'
        );
    }
    public function editAvatar($request, $template)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = '/images/' . time() . '.' . $image->getClientOriginalExtension();

            $oldImage = $template->avaPath;
            if ($oldImage) {
                $oldImagePath = public_path('images') . '/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image->move(public_path('images'), $imageName);
            $template->update(
                [
                'avaPath' => $imageName,
                ]
            );
            return $this->responseSuccess('Change Avatar successfully');
        }
        return $this->responseFail('Failed to change Avatar');
    }
}
