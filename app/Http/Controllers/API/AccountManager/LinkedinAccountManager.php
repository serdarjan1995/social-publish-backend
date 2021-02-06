<?php

namespace App\Http\Controllers\API\AccountManager;

use App\Http\Controllers\ApiController;
use App\Model\AccountManager\AccountManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LinkedinAccountManager extends ApiController
{

    public function userInfo($request) {
        $account_manager = AccountManager::where('user_id', Auth::id())
            ->where('profile_id', $request)
            ->where('social_network_id', 4)->first();
        return $account_manager;
    }

    public function getLinkedinAdminCompanyList($auth_token) {
        $response = Http::withToken($auth_token)->get("https://api.linkedin.com/v2/organizationalEntityAcls?q=roleAssignee");
        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }

    public function getLinkedinCompanyInfo($token, $id) {
        $response = Http::withToken($token)->get("https://api.linkedin.com/v2/organizations/".$id);
        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
    public function getLinkedinCompanyProfileImage($token, $id) {

        $response = Http::withToken($token)->get("https://api.linkedin.com/v2/organizations/".$id."?projection=(coverPhotoV2(original~:playableStreams,cropped~:playableStreams,cropInfo),logoV2(original~:playableStreams,cropped~:playableStreams,cropInfo))");
        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }

    public function getLinkedinAdminCompanyPages(Request $request){
        $auth_info = $this->userInfo($request->profile_id);
        $auth_linkedin_company_list = $this->getLinkedinAdminCompanyList($auth_info->auth_token);
        $body = json_decode($auth_linkedin_company_list['body']);
        $elements = $body->elements;

        $company_id_list = [];
        foreach ($elements as $data) {
            $datam = preg_replace('/[^.%0-9]/', '', $data->organizationalTarget);
            array_push($company_id_list, ['id'=> $datam]);
        }

        $all_info = [];
        foreach ($company_id_list as $data) {
            if (!AccountManager::where('user_id', Auth::id())->where('profile_id', $data['id'])->first()) {
                $company_list = $this->getLinkedinCompanyInfo($auth_info->auth_token, (string)$data['id']);
                $company_cover = $this->getLinkedinCompanyProfileImage($auth_info->auth_token, (string)$data['id']);

                $body_details = json_decode($company_list['body']);
                $body_cover = json_decode($company_cover['body']);

                $logoV2 = $body_cover->logoV2;
                $logoV2 = (array)$logoV2;
                array_push($all_info, [
                    'key' => $data['id'],
                    'name' => $body_details->localizedName,
                    'avatar_url' => $logoV2["original~"]->elements[0]->identifiers[0]->identifier
                ]);
            }
            error_log($data['id']);
            //$data['details'] = $data['id'];
        }

        return $all_info;
    }


    public function parentInfo($request) {
        $account_manager = AccountManager::where('user_id', Auth::id())
            ->where('id', $request)
            ->where('social_network_id', 4)->first();
        return $account_manager;
    }

    public function addLinkedinPages(Request $request) {

        $auth_info = $this->parentInfo($request->parent_id);

        foreach ($request->accounts as $data) {

            $company_cover = $this->getLinkedinCompanyProfileImage($auth_info->auth_token, (string)$data);
            $body_cover = json_decode($company_cover['body']);
            $logoV2 = $body_cover->logoV2;
            $logoV2 = (array)$logoV2;

            $company_list = $this->getLinkedinCompanyInfo($auth_info->auth_token, (string)$data);
            $body_details = json_decode($company_list['body']);

            AccountManager::create([
                'social_network_id' => 4,
                'user_id' => Auth::id(),
                'login_type' => 'oauth',
                'can_post' => true,
                'name' => $body_details->localizedName,
                'username' => null,
                'auth_token' => $auth_info->auth_token,
                'profile_id' => $data,
                'parent_id' => $request->parent_id,
                'avatar_url' => $logoV2["original~"]->elements[0]->identifiers[0]->identifier,
                'account_url' => "https://www.linkedin.com/company/".$data,
                'status' => 1,
                'category_id' => 2,
                'data' => null,
            ]);
        }

        return $this->success('OK', [
            'data'=>$request,
            'parent_id' => $request->parent_id,
            'accounts' => $request->accounts
        ]);
    }



}
