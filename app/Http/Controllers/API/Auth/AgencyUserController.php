<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\AuthUpdateRequest;
use App\Http\Requests\Auth\UserManagerUserCreateRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendMail;
use App\Notifications\AgencyUserSetInitialPasswordNotification;
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

class AgencyUserController extends ApiController
{
    public function createAgencyUser(UserManagerUserCreateRequest $request){
        RoleHelper::need("agency_user_create");
        $user = Auth::user();
        $user_name_surname = $user->name.' '.$user->surname;

        $user_create = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'default_lang' => App::getLocale(),
            'status' => true,
            'email_verified' => false,
            'email' => $request->email,
            'password' => 'nopasswordyet'
        ]);

        $user_create->assignRole($user_create, 6);

        if ($user_create) {
            SendMail::dispatch($user_create, new AgencyUserSetInitialPasswordNotification($user_name_surname,
                $user_create->name, App::getLocale()));
            return $this->success(trans('auth.register_success'));

        } else {
            return $this->fail("Creation failed");
        }
    }

    public function setAgencyUserInitialPassword(){
        /*if (!$request->hasValidSignature()){
            return $this->unauthorized(trans('auth.invalid_verify_url'));
        }
        $user = User::findOrFail($user_id);
        if (!$user->hasVerifiedEmail()){
            $user->markEmailAsVerified();
        }
        else{
            return $this->badRequest(trans('auth.already_verified'));
        }
        return $this->success(trans('auth.email_verify_success'));*/
    }


}
