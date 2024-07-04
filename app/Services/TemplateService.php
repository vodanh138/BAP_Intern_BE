<?php
namespace App\Services;


use App\Services\Interfaces\TemplateServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\TemplateRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\ShowRepositoryInterface;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class TemplateService implements TemplateServiceInterface
{
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some errors have occurred while generating token',
                    'error' => $e->getMessage()
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Log in successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'username' => $user->username,
                'role' => $user->hasRole('admin') ? 'ADMIN' : 'USER',
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Username or password incorect',
            ]);
        }
    }
    public function addTemplate($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 422);
        }
        $template = $this->templateRepository->createTemplate($request->name, 'lg', 'default-title', 'default-footer');
        if (!$template)
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to create template in database',
            ]);
        if (!$this->addSection($template->id))
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to create section in database',
            ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Create template successfully',
            'template' => $template,
        ]);
    }

    public function editTemplate($request, $template)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'title' => 'required|string',
            'footer' => 'required|string',
            'logo' => 'required|max:3',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 422);
        }
        $template->update([
            'name' => $request->name,
            'logo' => $request->logo,
            'title' => $request->title,
            'footer' => $request->footer,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Edit template successfully',
            'template' => $template,
        ]);
    }

    public function deleteTemplate($template)
    {
        $show = $this->showRepository->getShow();
        if ($show) {
            $temp = $this->templateRepository->getChosenTemplate($show);
            if ($template->id === $temp->id)
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Cannot delete the chosen template'
                ]);
        }
        $template->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Template deleted successfully'
        ]);
    }

    public function show()
    {
        $show = $this->showRepository->getShow();
        if (!$show) {
            return response()->json([
                'status' => 'fail',
                'message' => 'There is nothing to show'
            ]);
        }
        $chosenTemplate = $this->templateRepository->getChosenTemplate($show);
        if (!$chosenTemplate) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No template have been chosen'
            ]);
        }
        $query = $this->sectionRepository->selectSectionBelongTo($chosenTemplate->id)->get()->map(function ($section) {
            if ($section->type == 1) {
                return [
                    'type' => $section->type,
                    'title' => $section->title,
                    'content' => $section->content1,
                ];
            } else if ($section->type == 2) {
                return [
                    'type' => $section->type,
                    'title' => $section->title,
                    'content1' => $section->content1,
                    'content2' => $section->content2,
                ];
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Show successfully',
            'id' => $chosenTemplate->id,
            'logo' => $chosenTemplate->logo,
            'title' => $chosenTemplate->title,
            'footer' => $chosenTemplate->footer,
            'section' => $query,
        ]);
    }

    public function getTemplate($template)
    {
        $query = $this->sectionRepository->selectSectionBelongTo($template->id)->get()->map(function ($section) {
            return [
                'type' => $section->type,
                'title' => $section->title,
                'content1' => $section->content1,
                'content2' => $section->content2,
            ];
        });

        return response()->json([
            'status' => 'success',
            'id' => $template->id,
            'logo' => $template->logo,
            'title' => $template->title,
            'footer' => $template->footer,
            'section' => $query,
        ]);
    }
    public function cloneTemplate($template)
    {
        $newtemplate = $this->templateRepository->createTemplate($template->name, $template->logo, $template->title, $template->footer);
        if (!$newtemplate)
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to create template in database',
            ]);
        try {
            $this->sectionRepository->selectSectionBelongTo($template->id)->get()->map(function ($section) use ($newtemplate) {
                $this->sectionRepository->createSection($section->type, $section->title, $section->content1, $section->content2, $newtemplate->id);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Some errors have occurred while copying template',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Clone template successfully',
            'template' => $this->getTemplate($newtemplate),
        ]);
    }

    public function getAllTemplates()
    {
        $user = Auth::user();
        $show = $this->showRepository->getShow();
        return response()->json([
            'status' => 'success',
            'username' => $user->username,
            'chosen' => $show->template_id,
            'templates' => $this->templateRepository->getAllTemplate(),
        ]);
    }

    public function changeTemplate($template)
    {
        $show = $this->showRepository->getShow();
        $show->template_id = $template->id;
        $show->save();
        return $this->getTemplate($template);
    }
    public function addSection($template_id)
    {
        $section = $this->sectionRepository->createSection(1, 'default-title', 'default-content1', '', $template_id);
        if (!$section)
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to create section in database',
            ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Add section successfully',
            'section' => $section,
        ]);
    }
    public function deleteSection($section)
    {
        $count = $this->sectionRepository->selectSectionBelongTo($section->template_id)->count();
        if ($count === 1)
            return response()->json([
                'status' => 'fail',
                'message' => 'Cannot delete the only section'
            ]);

        $section->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Section deleted successfully'
        ]);
    }
    public function editSection($request, $Section)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|integer|max:2|min:1',
            'title' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 422);
        }

        $Section->update([
            'type' => $request->type,
            'title' => $request->title,
            'content1' => $request->input('content1', ''),
        ]);
        if ($request->type == 2)
            $Section->update([
                'content2' => $request->input('content2', ''),
            ]);
        else
            $Section->update([
                'content2' => '',
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Edit template successfully',
            'section' => $Section,
        ]);
    }

}
