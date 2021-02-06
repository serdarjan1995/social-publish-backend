<?php

namespace App\Http\Controllers\API\AccountManager;

use App\Helpers\FacebookSdkHelper;
use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\AccountManager\AccountIdRequiredRequest;
use App\Http\Requests\AccountManager\AddFacebookAccountRequest;
use App\Model\AccountManager\AccountCategory;
use App\Model\AccountManager\AccountManager;
use \Illuminate\Support\Facades\Auth;

class FacebookAccountManagerController extends ApiController
{
    private $facebook_sdk;
    private $facebook_pages_fields = ['id','name','username','can_post','fan_count','link','is_verified','picture','access_token','category'];
    private $facebook_groups_fields = ['id','name','email','cover','member_count'];

    public function __construct()
    {
        $this->facebook_sdk = (new FacebookSdkHelper())->getSdk();
    }


    public function getFacebookPages(AccountIdRequiredRequest $request){
        RoleHelper::need('account_show');

        $facebook_profile = AccountManager::where([['user_id',Auth::id()],
            ['social_network_id',1],['category_id',1]])->first();
        if(!$facebook_profile){
            return $this->fail(trans('account_manager.add_facebook_profile_first'));
        }
        return $this->success(null,['pages' => $this->fetchFacebookPages($facebook_profile,false)]);
    }


    public function fetchFacebookPages($facebook_profile,$fetch_access_token){

        $meUrl = '/me/accounts?fields='.implode(',', $this->facebook_pages_fields);

        $response = $this->facebook_sdk->get($meUrl,$facebook_profile->auth_token);
        $response_json = json_decode($response->getBody(), true);
        $response_data = [];
        foreach ($response_json['data'] as $account){
            $temp_account_data = [];
            $temp_account_data['id'] = $account['id'];
            $temp_account_data['name'] = $account['name'];
            $temp_account_data['link'] = $account['link'];
            $temp_account_data['fan_count'] = $account['fan_count'];
            $temp_account_data['category'] = $account['category'];
            $temp_account_data['avatar'] = $account['picture']['data']['url'];
            if($fetch_access_token){
                $temp_account_data['access_token'] = $account['access_token'];
                $temp_account_data['can_post'] = $account['can_post'];
            }
            array_push($response_data,$temp_account_data);
        }

        return $response_data;
    }


    public function getFacebookGroups(AccountIdRequiredRequest $request){
        RoleHelper::need('account_show');

        $facebook_profile = AccountManager::where([['user_id',Auth::id()],
            ['id',$request->id]])->first();
        if(!$facebook_profile){
            return $this->fail(trans('account_manager.add_facebook_profile_first'));
        }
        return $this->success(null,['groups' => $this->fetchFacebookGroups($facebook_profile,false)]);
    }


    public function fetchFacebookGroups($facebook_profile,$fetch_access_token){
        $meUrl = '/me/groups?fields='.implode(',', $this->facebook_groups_fields);

        $response = $this->facebook_sdk->get($meUrl,$facebook_profile->auth_token);
        $response_json = json_decode($response->getBody(), true);
        $response_data = [];
        foreach ($response_json['data'] as $account){
            $temp_account_data = [];
            $temp_account_data['id'] = $account['id'];
            $temp_account_data['name'] = $account['name'];
            $temp_account_data['fan_count'] = isset($account['member_count'])?$account['member_count']:null;
            $temp_account_data['avatar'] = isset($account['cover'])?$account['cover']['source']:null;
            if($fetch_access_token){
                $temp_account_data['access_token'] = $facebook_profile->auth_token;
            }
            array_push($response_data,$temp_account_data);
        }

        return $response_data;
    }


    public function addFacebookPages(AddFacebookAccountRequest $request){
        RoleHelper::need('account_add');
        return $this->addFacebookAccounts('page',$request->parent_id,$request->accounts);
    }

    public function addFacebookGroups(AddFacebookAccountRequest $request){
        RoleHelper::need('account_add');
        return $this->addFacebookAccounts('group',$request->parent_id,$request->accounts);
    }


    public function addFacebookAccounts($category,$parent_id,$add_accounts){
        $facebook_profile = AccountManager::where([
            ['user_id',Auth::id()],
            ['social_network_id',1],
            ['category_id',1],
            ['id',$parent_id]])->first();
        if(!$facebook_profile){
            return $this->fail(trans('account_manager.add_facebook_profile_first'));
        }

        $account_category = AccountCategory::where([['category',$category],['social_network_id',1]])->first();
        $accounts = [];
        if( $account_category->category === 'page'){
            $accounts = $this->fetchFacebookPages($facebook_profile,true);
        }
        else if ($account_category->category === 'group'){
            $accounts = $this->fetchFacebookGroups($facebook_profile,true);
        }
        $includes_id = false;
        $already_exists = false;
        foreach ($accounts as $account){
            foreach ($add_accounts as $id){
                if ($id === $account['id']){
                    try {
                        if (!AccountManager::where('profile_id',$id)->first()){
                            AccountManager::create([
                                'social_network_id' => $facebook_profile->__get('social_network_id'),
                                'user_id' => Auth::id(),
                                'login_type' => 'oauth',
                                'can_post' => isset($account['can_post'])?$account['can_post']:1,
                                'name' => $account['name'],
                                'username' => null,
                                'auth_token' => $account['access_token'],
                                'profile_id' => $account['id'],
                                'parent_id' => $facebook_profile->id,
                                'avatar_url' => $account['avatar'],
                                'account_url' => isset($account['link'])?$account['link']:'https://fb.me/'.$account['id'],
                                'status' => 1,
                                'category_id' => $account_category->id,
                                'data' => null,
                            ]);
                            $includes_id = true;
                        }
                        else{
                            $already_exists = true;
                        }

                    }
                    catch (\Exception $e){
                        return $this->fail(trans('account_manager.account_could_not_added'),[$e->getMessage()]);
                    }
                }
            }

        }

        if($includes_id){
            return $this->success(trans('account_manager.account_added'));
        }
        else if ($already_exists){
            return $this->fail(trans('account_manager.account_exists'));
        }
        else{
            return $this->fail(trans('account_manager.account_could_not_added'));
        }
    }


}
