<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Jobs\SendMail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends ApiController
{
    public function forgot(ForgotPasswordRequest $request) {
        Password::sendResetLink($request->only('email'));
        return $this->success(trans('passwords.sent'));
    }


    public function reset(ResetPasswordRequest $request) {
        $reset_password_status = Password::reset($request->validated(), function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return $this->badRequest(trans('passwords.invalid_token'));
        }

        return $this->success(trans('passwords.reset'));
    }
}
