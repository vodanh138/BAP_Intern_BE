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
        $template->name = 'default-name' . $template->id;
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
            return [
                'type' => $section->type,
                'title' => $section->title,
                'content1' => $section->content1,
                'content2' => $section->content2,
            ];
        });

        return response()->json([
            //'id' => $template->id,
            'logo' => $template->logo,
            'title' => $template->title,
            'footer' => $template->footer,
            'section' => $query,
        ]);
    }
    public function cloneTemplate($template)
    {
        $newtemplate = new Template();
        $newtemplate->name = $template->name;
        $newtemplate->logo = $template->logo;
        $newtemplate->title = $template->title;
        $newtemplate->footer = $template->footer;
        $newtemplate->save();
        $query = Section::where('template_id', $template->id)->get()->map(function ($section) use ($newtemplate) {
            $newsection = $this->addSection($newtemplate->id);
            $newsection->type = $section->type;
            $newsection->title = $section->title;
            $newsection->content1 = $section->content1;
            $newsection->content2 = $section->content2;
            $newsection->save();
        });
        return $this->getTemplate($newtemplate);
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
        return $this->getTemplate($template);
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
        return $section;
    }
    public function deleteSection($section)
    {
        $count = Section::where('template_id', $section->template_id)->count();
        if ($count === 1)
            return response()->json(['message' => 'Cannot delete the only section']);

        $section->delete();
        return response()->json(['message' => 'Section deleted successfully']);
    }
    public function editSection($request, $Section)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|integer|max:2|min:1',
            'title' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $Section->type = $request->type;
        $Section->title = $request->title;
        $Section->content1 = '';
        $Section->content2 = '';
        $Section->content1 = $request->input('content1', '');
        if ($request->type == 2)
            $Section->content2 = $request->input('content2', '');
        $Section->save();

        return $Section;
    }
    
}
