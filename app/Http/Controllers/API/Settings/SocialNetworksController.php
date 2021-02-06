<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Requests\Settings\ShowKeySocialNetworkRequest;
use App\Http\Resources\SocialNetworksResource;
use App\Model\SocialNetwork;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Settings\ListSocialNetworkRequest;
use App\Http\Requests\Settings\StoreSocialNetworkRequest;
use App\Http\Requests\Settings\UpdateSocialNetworkRequest;
use App\Http\Requests\Settings\ShowSocialNetworkRequest;
use App\Http\Requests\Settings\DeleteSocialNetworkRequest;
use App\Http\Requests\Settings\SetKeySocialNetworkRequest;
use App\Http\Requests\Settings\UpdateKeySocialNetworkRequest;
use App\Http\Requests\Settings\DeleteKeySocialNetworkRequest;
use App\Model\SocialNetworkApi;

class SocialNetworksController extends ApiController
{

    public function list(ListSocialNetworkRequest $request)
    {
        return $this->success('', ['list' => SocialNetwork::select(
            'id',
            'name',
            'icon',
            'color',
        )->get()]);
        //return new SocialNetworksResource(SocialNetwork::paginate(3));
    }

    public function add(StoreSocialNetworkRequest $request)
    {
        $store = SocialNetwork::create($request->all());

        if ($store) {
            SocialNetworkApi::create([
                'social_network_id' => $store->id
            ]);

            return $this->success('Sucess',['social'=> $store]);
       }

    }

    public function update(UpdateSocialNetworkRequest $request)
    {
        $social = SocialNetwork::findOrFail($request->id);
        $social->name = $request->input('name');
        $data = $social->save();
        return $this->success($data);
    }

    public function show(ShowSocialNetworkRequest $request)
    {
        $social = SocialNetwork::select(
            'id',
            'name',
            'icon',
            'color'
        )->findOrFail($request->id);
        return $this->success($social);
    }

    public function delete(DeleteSocialNetworkRequest $request)
    {
        $Sapi = SocialNetwork::findOrFail($request->id);
        $Sapi->delete();
        return $this->success(trans('social_networks_controller.deleted_success'));
    }

    public function setkey(SetKeySocialNetworkRequest $request)
    {
        $social = SocialNetworkApi::select('*')->where("social_network_id", $request->social_network_id)->first();
        $social->api_key = $request->api_key;
        $social->api_secret = $request->api_secret;
        $social->api_callback_url = $request->api_callback_url;
        $social->extra_settings = $request->extra_settings;

        return $this->success(trans('social_networks_controller.created_success'),['sas'=> $social->save()]);
    }

    public function getkey(ShowKeySocialNetworkRequest $request){
        $show = SocialNetworkApi::select(
            'id',
            'social_network_id',
            'api_key',
            'api_secret',
            'api_callback_url',
            'extra_settings')->where("social_network_id", $request->id)->first();
        return $this->success(trans('social_networks_controller.load_successfully'), ['socialnetwork_apikey' => $show]);
    }
}
