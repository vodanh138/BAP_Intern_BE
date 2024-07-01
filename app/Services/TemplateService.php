<?php
namespace App\Services;

use App\Models\Template;
use App\Models\Section;
use App\Models\Show;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TemplateService
{
    public function addTemplate()
    {
        $template = new Template();
        $template->name = 'default-name';
        $template->logo = 'lg';
        $template->title = 'default-title';
        $template->footer = 'default-footer';
        $template->save();
        $template->name = 'default-name'.$template->id;
        $template->save();
        $this->addSection($template->id);
        return $template;
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
            return response()->json(['error' => $validator->errors()], 422);
        }

        $template->name = $request->name;
        $template->logo = $request->logo;
        $template->title = $request->title;
        $template->footer = $request->footer;
        $template->save();

        return $template;
    }

    public function deleteTemplate($template)
    {
        $temp = Template::where('id', Show::first()->template_id)->first();
        if ($template->id === $temp->id)
            return response()->json(['message' => 'Cannot delete the chosen template']);

        $template->delete();
        return response()->json(['message' => 'Template deleted successfully']);
    }

    public function show()
    {
        $show = Show::first();
        if (!$show) {
            return response()->json(['message' => 'There are nothing to show']);
        }
        $temp = Template::where('id', Show::first()->template_id)->first();
        if (!$temp) {
            return response()->json(['message' => 'No template have been chosen']);
        }
        $query = Section::where('template_id', $temp->id)->get()->map(function ($section) {
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
            'logo' => $temp->logo,
            'title' => $temp->title,
            'footer' => $temp->footer,
            'section' => $query,
        ]);
    }

    public function getTemplate($template)
    {
        $query = Section::where('template_id', $template->id)->get()->map(function ($section) {
            if ($section->type == 1) {
                $section->content1 = $section->content1 . " " . $section->content2;
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
            'logo' => $template->logo,
            'title' => $template->title,
            'footer' => $template->footer,
            'section' => $query,
        ]);
    }

    public function getAllTemplates()
    {
        $user = Auth::user();
        return response()->json([
            'username' => $user->username,
            'templates' => Template::all(),
        ]);
    }

    public function changeTemplate($template)
    {
        $show = Show::first();
        $show->template_id = $template->id;
        $show->save();
        return response()->json(['message' => 'Template change successfully']);
    }
    public function addSection($template_id)
    {
        $section = new Section();
        $section->type = 1;
        $section->title = 'default-title';
        $section->content1 = 'default-content';
        $section->content2 = '';
        $section->template_id = $template_id;
        $section->save();
    }
    public function deleteSection($section)
    {
        $temp = Template::where('id', Show::first()->template_id)->first();
        if ($section->id === $temp->id)
            return response()->json(['message' => 'Cannot delete the chosen template']);

        $section->delete();
        return response()->json(['message' => 'Template deleted successfully']);
    }
}
