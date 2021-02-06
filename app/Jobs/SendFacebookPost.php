<?php

namespace App\Jobs;

use App\Helpers\ImageToken;
use App\Model\AccountManager\AccountManager;
use App\Model\FileManager;
use App\Model\Post;
use Facebook\Facebook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendFacebookPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $facebook_sdk;
    protected $post_data;
    protected $account;

    /**
     * Create a new job instance.
     *
     * @param Facebook $facebook_sdk
     * @param array $post_data
     * @param AccountManager $account
     */
    public function __construct($facebook_sdk,$post_data,$account)
    {
        $this->facebook_sdk = $facebook_sdk;
        $this->post_data = $post_data;
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->facebookPost($this->account,$this->post_data);
    }

    public function facebookPost($account,$post_data){
        $post_data['post_status'] = 0;
        $post = Post::create($post_data);
        $user_id = $account->__get('user_id');
        $endpoint = "";
        $params = [];
        switch ($post_data['post_type']){
            case 'text':
                $endpoint = '/'.$account->__get('profile_id').'/feed';
                $params = ['message' => $post_data['post_caption']];
                break;

            case 'media':
                $post_files = $post_data["post_data"]["files"];
                if ( count($post_files) == 1){
                    $file = FileManager::where('user_id',$user_id)->find($post_files[0]);
                    $url = ImageToken::getToken($file->url);
                    if ($file->type === 'image'){
                        $endpoint = '/'.$account->__get('profile_id').'/photos';
                        $params['url'] = $url;
                        $params['message'] = $post_data['post_caption'];
                    }
                    else if ($file->type === 'video'){
                        $endpoint = '/'.$account->__get('profile_id').'/videos';
                        $params['file_url'] = $url;
                        $params['description'] = $post_data['post_caption'];
                    }
                }
                else{
                    $upload_count = 0;
                    foreach ($post_files as $file_id){
                        $multiple_media_params = [];
                        $multiple_media_params['published'] = false;
                        $file = FileManager::where('user_id',$user_id)->find($file_id);
                        $url = ImageToken::getToken($file->url);
                        if ($file->type === 'image'){
                            $endpoint = '/'.$account->__get('profile_id').'/photos';
                            $multiple_media_params['url'] = $url;
                        }
                        else if ($file->type === 'video' && $account->__get('category_id') != 2){
                            $endpoint = '/'.$account->__get('profile_id').'/videos';
                            $multiple_media_params['file_url'] = $url;
                        }
                        else{
                            continue;
                        }
                        $response = $this->facebook_sdk->post(
                            $endpoint,
                            $multiple_media_params,
                            $account->__get('auth_token')
                        );
                        $response_json = json_decode($response->getBody(), true);
                        $params['attached_media['.$upload_count.']'] = '{"media_fbid":"'.$response_json['id'].'"}';
                        $upload_count++;
                    }
                    $endpoint = '/'.$account->__get('profile_id').'/feed';
                    $params['message'] = $post_data['post_caption'];
                }
                break;

            case 'link':
                $endpoint = '/'.$account->__get('profile_id').'/feed';
                $params = ['message' => $post_data['post_caption'],
                    'link' => $post_data["post_data"]['link']];
                break;

            default: break;

        }
        try {
            $response = $this->facebook_sdk->post(
                $endpoint,
                $params,
                $account->__get('auth_token')
            );
            $response_json = json_decode($response->getBody(), true);
            $post->post_status = 1;
            $post->post_id = $response_json['id'];
            $post->save();
        }catch (\Exception $e){
            $post->post_status = 2;
            $post->save();
            throw new \Exception($e->getMessage());
        }

    }
}
