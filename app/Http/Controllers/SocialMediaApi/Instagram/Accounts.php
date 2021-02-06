<?php

namespace App\Http\Controllers\SocialMediaApi\Instagram;
require (app_path('Http/libraries/vendor/autoload.php'));
use App\Http\Controllers\ApiController;
use App\Http\Requests\Instagram\InstagramLogin2FaRequest;
use App\Http\Requests\Instagram\InstagramLoginRequest;
use App\Model\AccountManager\AccountCategory;
use App\Model\AccountManager\AccountManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use InstagramAPI\Exception\AccountDisabledException;
use InstagramAPI\Exception\ChallengeRequiredException;
use InstagramAPI\Exception\CheckpointRequiredException;
use InstagramAPI\Exception\IncorrectPasswordException;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Exception\SentryBlockException;
use InstagramAPI\Instagram;
use InvalidArgumentException;
use Illuminate\Support\Facades\Cache;

class Accounts extends ApiController
{
    private $ig;
    private $username;
    private $password;
    private $security_code;

    public function __construct()
    {
        Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $this->ig = new Instagram(false, false, [
            'storage' => env('DB_CONNECTION'),
            'dbhost' => env('DB_HOST'),
            'dbname' => env('DB_DATABASE'),
            'dbusername' => env('DB_USERNAME'),
            'dbpassword' => env('DB_PASSWORD'),
            'dbtablename' => 'session_instagram'
        ]);
        $this->ig->setVerifySSL(false);
       // $this->ig->setProxy('127.0.0.1:9876');
    }



    public function login(InstagramLoginRequest $request)
    {
        Cache::put('login',$request->username,180);
        Cache::put('password',$request->password,180);
        try {
            $loginResponse = $this->ig->login($request->username, $request->password, 3000);
          return  $this->check_2fa($loginResponse);
        }
        catch (CheckpointRequiredException $e) {
            $this->clear_cookie($request->username);
            return $this->fail('Please login on instagram to pass checkpoint', ['instaErrors' => true]);
        } catch (AccountDisabledException $e) {
            return $this->fail('Your account has been disabled for violating instagram terms', ['instaErrors' => true]);
        } catch (SentryBlockException $e) {
            return $this->fail('Your account has been banned from instagram api for spam behaviour or otherwise abusing', ['instaErrors' => true]);
        } catch (IncorrectPasswordException $e) {
            return $this->fail("The password you entered is incorrect please try again", ['instaErrors' => true]);
        } catch (InstagramException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getMessage() == "consent_required") {
                    $this->clear_cookie($request->username);
                    return $this->fail("Go to instagram.com login with this account and then try to add this account again", ['instaErrors' => true]);
                }

                return $this->fail($e->getResponse()->getMessage(), ['instaErrors' => true]);

            } else {
                $message = explode(":", $e->getMessage(), 2);
                $message = explode(" (", end($message));

                return $this->fail($message[0], ['instaErrors' => true]);
            }
        } catch (\Exception $e) {
            return $this->fail("Oops, something went wrong please try again later", ['instaErrors' => true, 'err' => $e->getMessage()]);
        }
    }

    public function login_2fa(InstagramLogin2FaRequest $request)
    {
        try {
            $loginResponse = $this->ig->login(Cache::get('login'), Cache::get('password'));

            if ($loginResponse !== null && $loginResponse->isTwoFactorRequired()) {
                $twoFactorIdentifier = $loginResponse->getTwoFactorInfo()->getTwoFactorIdentifier();
                $this->ig->finishTwoFactorLogin(Cache::get('login'), Cache::get('password'), $twoFactorIdentifier, $request->verify_code);
                $this->check_2fa($loginResponse);
            }
        } catch (\Exception $e) {
            return $this->fail('Instagram error',['data' => $e->getMessage()]);
        }
    }

    private function check_2fa($res)
    {
        if (!is_null($res) && $res->isTwoFactorRequired()) {
            $phone_number = $res->getTwoFactorInfo()->getObfuscatedPhoneNumber();
            $twofa = $res->getTwoFactorInfo()->getTwoFactorIdentifier();
            return $this->success(sprintf("Enter the 6 digit code we sent to the number ending in %s", $phone_number),[
                'verify' => true,
                'twfa' => $twofa
            ]);
        }
        else{
            $userdata = json_decode($this->ig->account->getCurrentUser());
            $account_category = AccountCategory::select('id')
                ->where([['social_network_id',3]])->first();
            $account =  AccountManager::create([
                'social_network_id' => 3,
                'user_id' => Auth::id(),
                'login_type' => 'request',
                'can_post' => true,
                'name' => $userdata->user->full_name,
                'username' => $userdata->user->username,
                'auth_token' => Crypt::encryptString(Cache::get('password')),
                'profile_id' => $userdata->user->pk,
                'avatar_url' => $userdata->user->profile_pic_url,
                'account_url' => 'https://www.instagram.com/'.$userdata->user->username,
                'status' => 1,
                'category_id' => $account_category->id,
                'data' => json_encode($userdata),
            ]);
            Cache::pull('login');
            Cache::pull('password');
            return $this->success(
                'YeeeHooo 🤩 Success',['ig' => $account]
            );
        }
    }

    private function clear_cookie($username)
    {
        DB::table('session_instagram')->where('username', $username)->delete();
    }
}
