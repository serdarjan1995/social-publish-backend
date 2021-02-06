<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\RegistrationFormRequest;
use App\Jobs\SendMail;
use App\Notifications\EmailVerifyNotification;
use App\User;
use Illuminate\Support\Facades\App;

class RegistrationController extends ApiController
{
    public function register(RegistrationFormRequest $request)
    {
        $user = User::create($request->getAttributes());
        $user->assignRole($user,config('custom.registration_default_role'));
        SendMail::dispatch($user,new EmailVerifyNotification($user->name,App::getLocale()));
        return $this->success(trans('auth.register_success'));

    }
}
