<?php

namespace App\Services;

use App\Services\Interfaces\TemplateServiceInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\TemplateRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ShowRepositoryInterface;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\App;
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
                    'role' => $user->hasRole('super-admin') ? 'super-admin' : ($user->hasRole('admin') ? 'admin' : 'user'),
                ],
                'Log in successfully'
            );
        } else {
            return $this->responseFail(__('messages.login-F'));
        }
    }
    public function addTemplate($request)
    {
        $template = $this->templateRepository->getATemplateByName($request->name);
        if ($template) {
            return $this->responseFail(__('validation.unique'));
        }
        DB::beginTransaction();
        try {
            $template = $this->templateRepository->
                createTemplate(
                    $request->name,
                    1,
                    1,
                    'default-title',
                    '#64748B',
                    '#000000',
                    'default-footer',
                    '#64748B',
                    '#FFFFFF',
                    '/images/default-ava.png'
                );
            if (!$template) {
                return $this->responseFail(__('messages.tempCreate-F'));
            }
            if (!$this->addSection($template->id)) {
                return $this->responseFail(__('messages.secCreate-F'));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseFail(__('messages.errorAddingTem'), 500);
        }
        return $this->responseSuccess(
            [
                'template' => $template,
            ],
            __('messages.tempCreate-T')
        );
    }
    public function deleteTemplate($templateIds)
    {
        if (is_string($templateIds)) {
            $templateIds = explode(',', $templateIds);
        }
        if (!is_array($templateIds)) {
            if (!is_array($templateIds)) {
                return $this->responseFail('Invalid template_ids format.', 400);
            }
        }
        $show = $this->showRepository->getShow();
        foreach ($templateIds as $templateId) {
            if (!$this->templateRepository->getATemplate($templateId)) {
                return $this->responseFail(__('messages.template') . $templateId . __('messages.notFound'), 404);
            }
            if ($templateId == $show->template_id) {
                return $this->responseFail(__('messages.cantDelTemp') . $templateId . __('messages.chosenTemp'));
            }
        }
        $message = '';
        foreach ($templateIds as $templateId) {
            $template = $this->templateRepository->getATemplate($templateId);
            $oldImage = $template->avaPath;
            if ($oldImage && $oldImage != '/images/default-ava.png') {
                $oldImagePath = public_path() . '/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $template->delete();
            $message .= $templateId . ',';
        }
        $template = rtrim($message, ',');
        return $this->responseSuccess([], __('messages.template') . $message . __('messages.del-T'));
    }

    public function show()
    {
        $show = $this->showRepository->getShow();
        if (!$show) {
            return $this->responseFail(__('messages.showNothing'));
        }
        $chosenTemplate = $this->templateRepository->getATemplate($show->template_id);
        if (!$chosenTemplate) {
            return $this->responseFail(__('messages.noChosen'));
        }
        $query = $this->sectionRepository->selectSectionBelongTo($chosenTemplate->id)->get();

        return $this->responseSuccess(
            [
                'id' => $chosenTemplate->id,
                'headerType' => $chosenTemplate->headerType,
                'footerType' => $chosenTemplate->footerType,
                'title' => $chosenTemplate->title,
                'headerBgColor' => $chosenTemplate->headerBgColor,
                'headerTextColor' => $chosenTemplate->headerTextColor,
                'footer' => $chosenTemplate->footer,
                'footerBgColor' => $chosenTemplate->footerBgColor,
                'footerTextColor' => $chosenTemplate->footerTextColor,
                'avaPath' => $chosenTemplate->avaPath,
                'section' => $query,
            ],
            __('messages.show-T')
        );
    }

    public function getTemplate($template)
    {
        $query = $this->sectionRepository->selectSectionBelongTo($template->id)->get();
        return $this->responseSuccess(
            [
                'id' => $template->id,
                'headerType' => $template->headerType,
                'footerType' => $template->footerType,
                'title' => $template->title,
                'headerBgColor' => $template->headerBgColor,
                'headerTextColor' => $template->headerTextColor,
                'footer' => $template->footer,
                'footerBgColor' => $template->footerBgColor,
                'footerTextColor' => $template->footerTextColor,
                'avaPath' => $template->avaPath,
                'section' => $query,
            ]
        );
    }
    public function cloneTemplate($template, $request)
    {
        $template1 = $this->templateRepository->getATemplateByName($request->name);
        if ($template1) {
            return $this->responseFail(__('validation.unique'));
        }
        DB::beginTransaction();

        try {
            $newAvaPath = '';
            if ($template->avaPath) {
                $oldAvaPath = public_path($template->avaPath);
                if (file_exists($oldAvaPath)) {
                    $newAvaName = time() . '_' . basename($oldAvaPath);
                    $newAvaPath = '/images/' . $newAvaName;
                    copy($oldAvaPath, public_path($newAvaPath));
                } else {
                    $newAvaPath = '/images/default-ava.png';
                }
            }
            $newtemplate = $this->templateRepository->
                createTemplate(
                    $request->name,
                    $template->headerType,
                    $template->footerType,
                    $template->title,
                    $template->headerBgColor,
                    $template->headerTextColor,
                    $template->footer,
                    $template->footerBgColor,
                    $template->footerTextColor,
                    $newAvaPath
                );
            if (!$newtemplate) {
                return $this->responseFail(__('messages.tempCreate-F'));
            }
            try {
                $this->sectionRepository->
                    selectSectionBelongTo($template->id)->get()->map(function ($section) use ($newtemplate) {
                        $this->sectionRepository->createSection(
                            $section->type,
                            $section->title,
                            $section->content1,
                            $section->content2,
                            $section->bgColor,
                            $section->textColor,
                            $newtemplate->id
                        );
                    });
            } catch (\Exception $e) {
                return $this->responseFail($e->getMessage(), 500);
            }
            DB::commit();
            return $this->responseSuccess([
                'template' => $this->getTemplate($newtemplate)->original,
            ], __('messages.clone-T'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseFail(__('messages.clone-F'), 500);
        }
    }

    public function getAllTemplates()
    {
        try {
            $user = Auth::user();
            $show = $this->showRepository->getShow();
            return $this->responseSuccess([
                'username' => $user->username,
                'chosen' => $show->template_id,
                'templates' => $this->templateRepository->getAllTemplate(),
            ], __('messages.allTemp-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.allTemp-F'));
        }
    }

    public function changeTemplate($template)
    {
        try {
            $show = $this->showRepository->getShow();
            $show->update([
                'template_id' => $template->id,
            ]);
            return $this->getTemplate($template);
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.chooseTemp-F'));
        }
    }
    public function addSection($template_id)
    {
        try {
            $section = $this->sectionRepository->
                createSection(1, 'default-title', 'default-content1', '', '#F3F4F6', '#000000', $template_id);
            return $this->responseSuccess([
                'section' => $section,
            ], __('messages.secCreate-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.secCreate-F'));
        }
    }
    public function deleteSection($section)
    {
        $count = $this->sectionRepository->selectSectionBelongTo($section->template_id)->count();
        if ($count === 1) {
            return $this->responseFail(__('messages.delOnlySection'));
        }
        try {
            $section->delete();
            return $this->responseSuccess([], __('messages.secDel-T'));
        } catch (\Exception $e) {
            return $this->responseFail([], __('messages.secDel-F'));
        }
    }
    public function editSection($request, $Section)
    {
        try {
            $Section->update([
                'type' => $request->type,
                'title' => $request->title,
                'content1' => $request->input('content1', ''),
                'bgColor' => $request->bgColor,
                'textColor' => $request->textColor,
            ]);
            if ($request->type == 2) {
                $Section->update([
                    'content2' => $request->input('content2', ''),
                ]);
            } else {
                $Section->update([
                    'content2' => '',
                ]);
            }
            return $this->responseSuccess([
                'section' => $Section,
            ], __('messages.secEdit-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.secEdit-F'));
        }
    }

    public function editHeader($request, $templateId)
    {
        $template = $this->templateRepository->getATemplate($templateId);

        if (!$template) {
            return $this->responseFail(__('messages.template') . $templateId . __('messages.notFound'), 404);
        }
        try {
            $template->update([
                'headerType' => $request->headerType,
                'title' => $request->input('title', ''),
                'headerBgColor' => $request->headerBgColor,
                'headerTextColor' => $request->headerTextColor,
            ]);
            return $this->responseSuccess([
                'template' => $template,
            ], __('messages.headerEdit-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.headerEdit-F'));
        }
    }

    public function editFooter($request, $templateId)
    {
        $template = $this->templateRepository->getATemplate($templateId);

        if (!$template) {
            return $this->responseFail(__('messages.template') . $templateId . __('messages.notFound'), 404);
        }

        try {
            $template->update([
                'footerType' => $request->footerType,
                'footer' => $request->input('footer', ''),
                'footerBgColor' => $request->footerBgColor,
                'footerTextColor' => $request->footerTextColor,
            ]);
            return $this->responseSuccess([
                'template' => $template,
            ], __('messages.footerEdit-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.footerEdit-F'));
        }
    }
    public function editAvatar($request, $template)
    {
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imageName = '/images/' . time() . '.' . $image->getClientOriginalExtension();

                $oldImage = $template->avaPath;
                if ($oldImage && $oldImage != '/images/default-ava.png') {
                    $oldImagePath = public_path() . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image->move(public_path('images'), $imageName);
                $template->update([
                    'avaPath' => $imageName,
                ]);
                return $this->responseSuccess(__('messages.avaEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.avaEdit-F'));
            }
        }
        return $this->responseFail(__('messages.avaEdit-F'));
    }
    public function loginProcessingLocale($username, $password, $locale)
    {
        if (!in_array($locale, ['en', 'vn'])) {
            return $this->responseFail(__('messages.changelang-NF'), 404);
        }
        try {
            session(['locale' => $locale]);
            App::setLocale($locale);
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.changelang-F'));
        }
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
                    'role' => $user->hasRole('super-admin') ? 'super-admin' : ($user->hasRole('admin') ? 'admin' : 'user'),
                ],
                __('messages.login-T')
            );
        } else {
            return $this->responseFail(__('messages.login-F'));
        }
    }
}
