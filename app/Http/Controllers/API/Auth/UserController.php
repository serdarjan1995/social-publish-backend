<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\AuthUpdateRequest;
use App\Http\Requests\Auth\UserManagerUserCreateRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendMail;
use App\Notifications\EmailVerifyNotification;
use App\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\UserManagerUserUpdateRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\ImageToken;

use Intervention\Image\ImageManagerStatic as Image;

class UserController extends ApiController
{
    public function profile()
    {
        RoleHelper::need("user_profile_show");
        $user_profile = User::select(
            'name',
            'surname',
            'profile_image',
            'default_lang',
            'phone_number',
            'email')->where('id', auth()->id())->first();

        if (!empty($user_profile['profile_image'])) {
            $user_profile->profile_image = ImageToken::getToken($user_profile['profile_image']);
        }
        return $this->success(null, ['profile' => $user_profile]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        RoleHelper::need("user_show");

        $user_all = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where("user_roles.role_id", '!=', 1)
            ->select('users.id', 'users.name', 'users.surname', 'users.email_verified', 'users.phone_number', 'users.status', 'users.email', 'user_roles.role_id', 'roles.name as role_name')
            ->get();

        if ($user_all){
            return $this->success($user_all);
        } else {
            return $this->fail("Database error");
        }
    }

    public function createProfileImage($request) {
        $profile_image = uniqid().'.'.$request->profile_image->getClientOriginalExtension();
        $request->profile_image->move(public_path('profile_image'),$profile_image);
        $request->profile_image=$profile_image;

        $img_path = 'profile_image/' . $profile_image;

        $image = Image::make($img_path)->resize(200, 200);
        if ($image->save()) {
            return $img_path;
        } else {
            return '';
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserManagerUserCreateRequest $request)
    {
        RoleHelper::need("user_create");
        if ($request->role_id == null) {
            $request->role_id   = 5;
        }
        if ($request->status == null) {
            $request->status    = 0;
        }
        if ($request->email_verified == null) {
            $request->email_verified    = 0;
        }
        $users_control = UserRole::where('user_id', Auth::id())->first();
        if ($users_control) {
            if ($request->role_id === 1) {
                return $this->fail('You can not add SuperAdmin');
            }

            if ($request->hasFile('profile_image')) {
                $profile_image = $this->createProfileImage($request);
            } else {
                $profile_image = '';
            }

            $user_create = User::create([
                'id' => Str::uuid()->toString(),
                'name' => $request->name,
                'surname' => $request->surname,
                'profile_image' => $profile_image,
                'default_lang' => App::getLocale(),
                'phone_number' => $request->phone_number,
                'status' => $request->status == 1 ? true : false,
                'email_verified' => $request->email_verified == 1 ? true : false,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user_create->assignRole($user_create,$request->role_id);

            if ($user_create) {
                if ($request->email_verified === 0) {
                    SendMail::dispatch($user_create, new EmailVerifyNotification($user_create->name,App::getLocale()));
                    return $this->success(trans('auth.register_success'));
                } else {
                    return $this->success("Created success");
                }
            } else {
                return $this->fail("Creation failed");
            }
        } else {
            return $this->fail('User not found');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return UserResource
     */
    public function show(User $user)
    {
        RoleHelper::need("user_show");
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateuser(UserManagerUserUpdateRequest $request)
    {
        RoleHelper::need("user_edit");
        if ($request->role_id == null) {
            $request->role_id   = 5;
        }
        if ($request->status == null) {
            $request->status    = 0;
        }
        if ($request->email_verified == null) {
            $request->email_verified    = 0;
        }

        // Update User Info
        $user_info = User::where('id', $request->id)->first();
        if ($user_info->email != $request->email) {
            if (User::where('email', $request->email)->first()){
                return $this->fail('Available e-mail failed');
            } else {
                $users_table = User::where('id', $request->id)->update([
                    'name' => $request->name,
                    'surname' => $request->surname,
                    'profile_image' => $request->profile_image,
                    'phone_number' => $request->phone_number,
                    'status' => $request->status,
                    'email' => $request->email,
                    'email_verified' => $request->email_verified,
                ]);
            }
        } else {
            $users_table = User::where('id', $request->id)->update([
                'name' => $request->name,
                'surname' => $request->surname,
                'profile_image' => $request->profile_image,
                'phone_number' => $request->phone_number,
                'status' => $request->status,
                'email_verified' => $request->email_verified,
            ]);
        }

        $user_role = UserRole::where('user_id', $request->id)->update([
            'role_id' => $request->role_id
        ]);

        if ($users_table || $user_role) {
            return $this->success('User update success');
        } else {
            return $this->fail('User update failed');
        }
    }


    public function authUpdateProfile(AuthUpdateRequest $request) {
        RoleHelper::need("user_profile_edit");

        User::where('id', Auth::id())->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
        return $this->success('User updated succes');
    }


    public function authUpdatePassword(Request $request){
        RoleHelper::need("profile_password_edit");

        if (!empty($request->password)) {
            if ($request->password != $request->passwordRepeat) {
                return $this->success('Passwords do not match.');
            } else {
                User::where('id', Auth::id())->update([
                    'password' => Hash::make($request->password),
                ]);
                return $this->success('User update success', []);
            }
        } else {
            return $this->success('Password is required.');
        }
    }

    public function userstatus(Request $request) {
        RoleHelper::need("user_edit");

        $status = $request->type ? 1 : 0;
        $selectedData = $request['selectedData'];

        foreach ($selectedData as $data) {
            $statusChange = User::where('id', '=', $data['id'])->update([
               'status' => $status  === 1 ? true : false
            ]);
        }
        if ($statusChange) {
            return $this->success('User status update success');
        } else {
            return $this->fail("Database failed");
        }
    }

    public function userverified(Request $request) {
        RoleHelper::need("user_edit");
        $statusChange = User::where('id', '=', $request->id)->update([
            'email_verified' => $request->type
        ]);
        if ($statusChange) {
            return $this->success("Success");
        } else {
            return $this->fail("Database failed");
        }
    }

}
