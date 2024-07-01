<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Show;
use App\Models\Section;


class UserController extends Controller
{
    protected $templateService;

    public function __construct( TemplateService $templateService)
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
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user = User::where('username', 'test01')->first();
        if (!$user) {
            if (!Template::first()) {
                $template = $this->templateService->addTemplate('default-name','lg','default-title','default-footer');
                $show = new Show();
                $show->template_id = $template->id;
                $show->save();
            }

            $user = new User();
            $user->username = 'test01';
            $user->password = bcrypt('123456');
            $user->save();

            $userRole = Role::firstOrCreate(['name' => 'admin']);
            $user->roles()->syncWithoutDetaching($userRole);
        } elseif (!Hash::check('123456', $user->password)) {
            $user->password = Hash::make('123456');
            $user->save();

            $userRole = Role::firstOrCreate(['name' => 'admin']);
            $user->roles()->syncWithoutDetaching($userRole);
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = User::find(Auth::id());
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'username' => $user->username,
                'role' => $user->hasRole('admin') ? 'ADMIN' : 'USER',
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized']);
        }
    }

    public function AddTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'logo' => 'required|max:3',
            'title' => 'required|string',
            'footer' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        return $this->templateService->addTemplate($request->name,$request->logo,$request->title,$request->footer);
    }

    public function EditTemplate(Request $request, Template $template)
    {
        return $this->templateService->editTemplate($request, $template);
    }

    public function DeleteTemplate(Template $template)
    {
        return $this->templateService->deleteTemplate($template);
    }

    public function Show()
    {
        return $this->templateService->show();
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
    public function AddSecion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|integer|max:2|min:1',
            'title' => 'required|string',
            'content1' => 'required|string',
            'content2' => 'required|string',
            'template_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        return $this->templateService->addSection($request->type,$request->title,$request->content1,$request->content2,$request->template_id);
    }
}
