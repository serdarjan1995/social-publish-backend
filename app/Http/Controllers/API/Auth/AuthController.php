<?php
namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\AuthUpdateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        $token = null;
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->unauthorized(trans('auth.error_login'));
        } else if (!$user->hasVerifiedEmail()) {
            return $this->unauthorized(trans('auth.error_email_not_verified'));
        }

        if (!$token = auth()->attempt($credentials)) {
            return $this->unauthorized(trans('auth.error_login'));
        }
        $data = $this->respondWithToken($token);
        return $this->success(null, $data);
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->success(trans('auth.error_logout'));
    }

    public function refresh()
    {
        try {
            return $this->success(null, $this->respondWithToken(auth()->refresh()));
        }
        catch (JWTException $e){
            return $this->require_login(trans('api.login_required'));
        }

    }

    private function respondWithToken($token)
    {
        return [
            'token' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth()->factory()->getTTL(),
            ]
        ];
    }
}
