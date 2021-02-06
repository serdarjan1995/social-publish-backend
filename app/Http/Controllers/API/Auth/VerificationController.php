<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Notifications\EmailVerifyNotification;
use Illuminate\Http\Request;
use App\User;
class VerificationController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['verify']);
    }

    public function verify($user_id,Request $request)
    {
        if (!$request->hasValidSignature()){
            return $this->unauthorized(trans('auth.invalid_verify_url'));
        }
        $user = User::findOrFail($user_id);
        if (!$user->hasVerifiedEmail()){
            $user->markEmailAsVerified();
        }
        else{
            return $this->badRequest(trans('auth.already_verified'));
        }
        return $this->success(trans('auth.email_verify_success'));
    }


    //TODO
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->badRequest('Email Already Verified');
        }

        auth()->user()->sendNotification(new EmailVerifyNotification());

        return $this->success("Email verification link sent on your email id");
    }
}
