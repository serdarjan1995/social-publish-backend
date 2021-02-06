<?php

namespace App\Http\Controllers\API\AccountManager;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\AccountManager\AccountIdRequiredRequest;
use App\Http\Requests\AccountManager\AddAccountFromCodeRequest;
use App\Http\Requests\AccountManager\GetAddAccountLinkRequest;
use App\Http\Requests\AccountManager\GetTwitterAccessRequest;
use App\Model\AccountManager\AccountManager;
use App\Model\AccountManager\AccountCategory;
use App\Model\AccountManager\AccountAddLinks;
use App\Model\SocialNetwork;
use App\Model\SocialNetworkApi;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class AccountManagerController extends ApiController
{
    private $facebook_api_v;
    private $linkedInScopes = ['rw_organization_admin', 'r_emailaddress', 'r_organization_social',
        'w_organization_social', 'w_member_social', 'r_liteprofile'];
    private $facebook_scopes = [
        'pages_read_user_content',
        'pages_read_engagement',
        /*'publish_pages',
        'manage_pages',*/
        'pages_manage_posts',
        'pages_manage_engagement',
        'publish_video',
        'publish_to_groups',
        'instagram_basic',
        /*'instagram_manage_comments',
        'instagram_manage_insights'*/];

    public function __construct()
    {
        $facebook_api_settings = SocialNetworkApi::where('social_network_id',1)->first();
        $extra_settings = json_decode($facebook_api_settings->extra_settings);
        $this->facebook_api_v = $extra_settings->api_version;
    }

    public function getAll()
    {
        RoleHelper::need('account_show');

        $accounts = AccountManager::select(
            'account_manager.id',
            'status',
            'account_manager.social_network_id',
            'account_manager.name',
            'username',
            'profile_id',
            'avatar_url',
            'account_url',
            'category',
            'data')
            ->where('user_id', Auth::id())
            ->join('account_category', 'category_id', '=', 'account_category.id')
            ->get();
        return $this->success(null,['accounts' => $accounts]);
    }

    public function getAllGrouped()
    {
        RoleHelper::need('account_show');

        $user = Auth::id();
        $socialNetworksUser = AccountManager::select('social_network_id as social_network')
            ->where('user_id', $user)
            ->distinct()
            ->get();
        $resData = [];
        foreach ($socialNetworksUser as $socialNetwork) {
            $s_data = [];
            $s_data['social_network'] = SocialNetwork::select('id','name','icon')
                ->where('id',$socialNetwork->social_network)->get();
            $s_data['accounts'] = AccountManager::select(
                'account_manager.id',
                'name',
                'username',
                'category',
                'avatar_url',
                'account_url',
                'data',)
                ->where([['account_manager.social_network_id',$socialNetwork->social_network],['user_id',$user]])
                ->join('account_category', 'category_id', '=', 'account_category.id')
                ->get();
            $resData = array_merge($resData,[$s_data]);
        }

        return $this->success(null,['accountGroups' => $resData]);
    }


    public function getAccessTokenFromCode(AddAccountFromCodeRequest $request)
    {
        RoleHelper::need('account_add');

        $social_network = SocialNetwork::find($request->social_network_id);
        if(!$social_network){
            return $this->fail(trans('global.not_found'));
        }
        try{
            switch ($request->social_network_id){
                case 1:
                    $userdata = Socialite::with(Str::lower($social_network->name))->stateless()
                        ->usingGraphVersion($this->facebook_api_v)->user();

                    break;
                default: $userdata = Socialite::with(Str::lower($social_network->name))->stateless()->user();
            }
            /****** Uncomment for restricting duplicate accounts *******/
            $account = AccountManager::where([['profile_id',$userdata->id],['user_id',Auth::id()]])->first();
            if($account){
                return $this->fail(trans('account_manager.account_exists'));
            }
            $account_category = AccountCategory::select('id')
                ->where([['social_network_id',$social_network->id]])->first();
            $account =  AccountManager::create([
                'social_network_id' => $social_network->id,
                'user_id' => Auth::id(),
                'login_type' => 'oauth',
                'can_post' => true,
                'name' => $userdata->name,
                'username' => (isset($userdata->username)?$userdata->username: isset($userdata->email))?$userdata->email:$userdata->name,
                'auth_token' => $userdata->token,
                'profile_id' => $userdata->id,
                'avatar_url' => $userdata->avatar,
                'account_url' => isset($userdata->profileUrl) ? $userdata->profileUrl : '',
                'status' => 1,
                'category_id' => $account_category->id,
                'data' => null,
            ]);
        }
        catch(\Exception $e){
            return $this->fail(trans('account_manager.account_could_not_added'));
        }
        return $this->success(trans('account_manager.account_added'));
    }

    public function getTwitterAccess(GetTwitterAccessRequest $request)
    {
        RoleHelper::need('account_add');

        $social_network = SocialNetwork::find($request->social_network_id);
        if(!$social_network){
            return $this->fail(trans('global.not_found'));
        }
        try{
            $connection = new TwitterOAuth(config('services.twitter.client_id'),
                config('services.twitter.client_secret'), $request->oauth_token, \Cache::get($request->user));
            $access_token = $connection->oauth("oauth/access_token", ["oauth_token" => $request->oauth_token,
                "oauth_verifier" => $request->oauth_verifier]);

            $connection = new TwitterOAuth(config('services.twitter.client_id'),
                config('services.twitter.client_secret'), $access_token['oauth_token'], $access_token['oauth_token_secret']);
            $content = $connection->get("account/verify_credentials");

            $account_category = AccountCategory::select('id')
                ->where([['social_network_id',$social_network->id]])->first();

            $account =  AccountManager::create([
                'social_network_id' => $social_network->id,
                'user_id' => Auth::id(),
                'login_type' => 'oauth',
                'can_post' => true,
                'name' => $content->name,
                'username' => $content->screen_name,
                'auth_token' => json_encode($access_token),
                'profile_id' => $content->id,
                'avatar_url' => $content->profile_image_url_https,
                'account_url' => 'https://twitter.com/'.$content->screen_name,
                'status' => 1,
                'category_id' => $account_category->id,
                'data' => null,
            ]);
        }
        catch(\Exception $e){
            return $this->fail(trans('account_manager.account_could_not_added'),[$e->getMessage()]);
        }
        return $this->success(trans('account_manager.account_added'));
    }

    public function getAddAccountLink(GetAddAccountLinkRequest $request){
        RoleHelper::need('account_add');

        //GET SOCIAL NETWORK ID & CATEGORY ID
        $social_network = SocialNetwork::find($request->social_network_id);
        if(!$social_network){
            return $this->fail(trans('global.not_found'));
        }
        $category = AccountAddLinks::select('type','uri')
            ->join('account_category', 'category_id', '=', 'account_category.id')
            ->where([['account_category.social_network_id',$request->social_network_id],
                ['account_category.category',$request->category]])->first();

        //IF'ING ALL SOCIAL MEDIAS
        if(!$category){
            return $this->fail(trans('global.not_found'));
        }
        else if($request->social_network_id == 3 && $category->type === 'oauth'){
            $category->uri = Socialite::with('instagram')->stateless()->redirect()->getTargetUrl();
            return $this->success(null,['link' => $category]);
        }
        else if($request->social_network_id == 1 && $request->category === 'profile' && $category->type === 'oauth'){
            $category->uri = Socialite::with('facebook')
                ->scopes($this->facebook_scopes)
                ->usingGraphVersion($this->facebook_api_v)->stateless()->redirect()->getTargetUrl();
            return $this->success(null,['link' => $category]);
        }
        else if($request->social_network_id == 9){
            $category->uri = Socialite::with('vkontakte')->stateless()->redirect()->getTargetUrl();
            return $this->success(null,['link' => $category]);
        }
        else if($request->social_network_id == 4){
            $category->uri = Socialite::with(Str::lower($social_network->name))
                ->scopes($this->linkedInScopes)
                ->stateless()->redirect()->getTargetUrl();
            return $this->success(null,['link' => $category]);
        }
        else if ($request->social_network_id == 2) {
            $tempId = Str::random(40);

            $connection = new TwitterOAuth(config('services.twitter.client_id'), config('services.twitter.client_secret'));
            $requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => config('services.twitter.redirect')
                . '?user=' . $tempId));

            Cache::put($tempId, $requestToken['oauth_token_secret'], 1);
            $category->uri = $connection->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token']));
            return $this->success(null, ['link' => $category]);
        }
        else{
            $category->uri = Socialite::with(Str::lower($social_network->name))->stateless()->redirect()->getTargetUrl();
            return $this->success(null,['link' => $category]);
        }
    }

    public function changeStatus(AccountIdRequiredRequest $request)
    {
        RoleHelper::need('account_edit');

        $account = AccountManager::where([['id',$request->only('id')],['user_id',Auth::id()]])->first();
        if (!$account){
            return $this->fail(trans('account_manager.account_not_found'));
        }
        else{
            $str = '';
            if($account->status){
                $account->status = 0;
                $str = 'disabled';
            }
            else{
                $account->status = 1;
                $str = 'enabled';
            }
            $account->save();
            return $this->success(trans('account_manager.account_'.$str));
        }

    }

    public function delete(AccountIdRequiredRequest $request)
    {
        RoleHelper::need('account_delete');

        $account = AccountManager::where([['id',$request->only('id')],['user_id',Auth::id()]])->first();
        if (!$account){
            return $this->fail(trans('account_manager.account_not_found'));
        }
        else{
            $account->delete();
            return $this->success(trans('account_manager.account_deleted'));
        }
    }

    public function getCategories(){
        RoleHelper::need('account_show');

        $categories = AccountCategory::select(
            DB::raw("social_network_id,GROUP_CONCAT(category SEPARATOR ',') as category"))
            ->groupBy('social_network_id')
            ->get();
        foreach ($categories as $category){
            $str_arr = explode(',',$category->category);
            $category->category = $str_arr;
        }
        return $this->success(null,['categories' => $categories]);
    }
}
