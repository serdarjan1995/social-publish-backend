<?php


namespace App\Helpers;


use App\Model\SocialNetworkApi;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookSdkHelper
{
    private $sdk;

    public function __construct()
    {
        $facebook_api_settings = SocialNetworkApi::where('social_network_id',1)->first();
        $extra_settings = json_decode($facebook_api_settings->extra_settings);
        $facebook_api_v = $extra_settings->api_version;

        try {
            $this->sdk = new Facebook([
                'app_id' => env('FACEBOOK_APP_ID'),
                'app_secret' => env('FACEBOOK_APP_SECRET'),
                'default_graph_version' => $facebook_api_v
            ]);
        } catch (FacebookSDKException $e) {
            header('Content-Type: application/json');
            echo '{"status": "'.trans('api.failure').'", "errors": true, "locale": "'
                .app()->getLocale().'","data":{"message":"Something went wrong with Facebook API"}}';
            exit;
        }
    }

    /**
     * @return Facebook
     */
    public function getSdk(): Facebook
    {
        return $this->sdk;
    }


}
