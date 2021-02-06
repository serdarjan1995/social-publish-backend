<?php

namespace App\Http\Controllers\API\Post;

use App\Helpers\FacebookSdkHelper;
use App\Helpers\ImageToken;
use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Post\GetLinkInfoRequest;
use App\Http\Requests\Post\PostIdRequiredRequest;
use App\Http\Resources\CalendarResource;
use App\Http\Resources\PostResource;
use App\Http\Requests\Post\PostAddRequest;
use App\Jobs\SendFacebookPost;
use App\Jobs\StartLiveStream;
use App\Jobs\SendInstagramPost;
use App\Jobs\SendTwitterPost;
use App\Model\AccountManager\AccountManager;
use App\Model\FileManager;
use App\Model\Post;
use App\Http\Controllers\SocialMediaApi\Linkedin\Post as Linkedin;
use DOMDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PostController extends ApiController
{
    private $facebook_sdk;

    public function __construct()
    {
        $this->facebook_sdk = (new FacebookSdkHelper())->getSdk();
    }

    public function getLinkInfo(GetLinkInfoRequest $request){
        RoleHelper::need('post_social_create');
        $url = $request->__get('url');
        $parsed_url = parse_url($url);
        $info = array(
            'title' => "",
            'description' => "",
            'image' => "",
            'host' => $parsed_url['host']
        );

        $youtube_reg_domain = "/(www\.)?(youtube.com|youtu.be)/";

        $is_youtube_domain = preg_match($youtube_reg_domain, $parsed_url['host'], $match);
        if($is_youtube_domain){
            try {
                $response = Http::get("https://www.youtube.com/oembed?url=".$url."&format=json");
                $result = json_decode($response);
                if(!empty($result)){

                    if(isset($result->title))
                        $info['title'] = $result->title;

                    if(isset($result->thumbnail_url))
                        $info['image'] = $result->thumbnail_url;

                    if(isset($result->author_name))
                        $info['description'] = $result->author_name;

                    return $this->success(null, ['info' => $info]);
                }
            }
            catch (\Exception $e){

            }
            return $this->fail('URL - '.trans('global.not_found'));
        }

        $response = Http::get($url);
        if ($response->status() != 200){
            return $this->fail('URL - '.trans('global.not_found'));
        }
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($response->body());
        $title = $doc->getElementsByTagName('title');
        $info["title"] = isset($title->item(0)->nodeValue) ? $title->item(0)->nodeValue : "";

        $metas = $doc->getElementsByTagName('meta');
        foreach ($metas as $meta){
            if($info['description'] == "" && strtolower($meta->getAttribute('name')) == 'description'){
                $info['description'] = $meta->getAttribute('content');
            }
            if($info['description'] == "" && strtolower($meta->getAttribute('property')) == 'og:description'){
                $info['description'] = $meta->getAttribute('content');
            }
            if($info['image'] == ""){
                if($meta->getAttribute('property') == 'og:image'){
                    $info['image'] = $meta->getAttribute('content');
                }
            }
        }

        if($info['image'] == ""){
            $link_icons = $doc->getElementsByTagName('link');
            foreach ($link_icons as $link_icon){
                if($link_icon->getAttribute('rel') == 'icon'){
                    $info['image'] = 'http://'.$parsed_url['host'].$link_icon->getAttribute('href');
                    break;
                }
            }

        }

        if($info['description'] == ""){
            $info['description'] = "...";
        }


        return $this->success(null, ['info' => $info]);
    }

    public function getAll()
    {
        RoleHelper::need('post_social_list');

        $cal = Post::select(
            'posts.id',
            'posts.account_id',
            'posts.post_caption',
            'posts.post_data',
            'posts.post_schedule',
            'posts.created_at',
            'posts.updated_at',
            'account_manager.name as ac_name',
            'account_manager.account_url',
            'account_manager.social_network_id',
            'social_networks.name',
            'social_networks.icon',
            'social_networks.color')
            ->join('account_manager', 'posts.account_id', '=', 'account_manager.id')
            ->join('social_networks', 'account_manager.social_network_id', '=', 'social_networks.id')
            ->where('posts.user_id', '=', Auth::id())
            ->get();

        $data = [
            'calendar' => CalendarResource::collection($cal),
        ];
        return $this->success('', $data);
    }

    public function add(PostAddRequest $request)
    {
        RoleHelper::need('post_social_create');
        if(($request->__get('post_type') == 'media'
            || $request->__get('post_type') == 'photo'
            || $request->__get('post_type') == 'video'
            || $request->__get('post_type') == 'carousel'
            || $request->__get('post_type') == 'story'
            || $request->__get('post_type') == 'livestream') &&
            !isset($request->__get('post_data')['files'])){
            return $this->fail('File '.trans('global.not_found'));
        }

        if($request->__get('post_type') == 'link' && !isset($request->__get('post_data')['link'])){
            return $this->fail('Link '.trans('global.not_found'));
        }

        $post_schedule = $request->__get('post_schedule');
        $account_ids = $request->__get('account_ids');

        $user = Auth::user();
        $user_id = Auth::id();

        $user_accounts = $user->accounts()->get();
        foreach ($account_ids as $account_id) {
            $check = false;
            foreach ($user_accounts as $user_account) {
                if ($account_id == $user_account->id) {
                    $check = true;
                    continue;
                }
            }
            if (!$check) {
                return $this->fail(trans('post.account_does_not_exists'));
            }
        }

        $post_data = [
            'user_id' => $user_id,
            'post_caption' => $request->__get('post_caption'),
            'post_title' => $request->__get('post_title'),
            'post_data' => $request->__get('post_data'),
            'post_type' => $request->__get('post_type'),
            'post_schedule' => !$post_schedule ? null : $post_schedule
        ];

        if ($post_data['post_type'] == 'livestream'){
            $this->liveStreamPost($account_ids, $post_data);
            return $this->success(trans('api.success'));
        }

        foreach ($account_ids as $account_id) {
            $account = (new AccountManager)->where([['user_id', $user_id], ['id', $account_id]])->first();
            $post_data['account_id'] = $account->id;


            switch ($account->__get('social_network_id')) {
                case 1:
                    if ($post_data['post_type'] === 'photo' || $post_data['post_type'] === 'video') {
                        $post_data['post_type'] = 'media';
                    }
                    SendFacebookPost::dispatch($this->facebook_sdk, $post_data, $account);
                    break;

                case 2:
                    //$this->twitterPost($account, $post_data);
                    SendTwitterPost::dispatch($account, $post_data);
                    break;

                case 3:
                    SendInstagramPost::dispatch($post_data,$account,$account_id);
                    break;

                case 4:
                    $linkedinPost = new Linkedin();
                    $result = $linkedinPost->sendPost($account, $post_data);
                    break;
                default:
                    return $this->fail(trans('global.not_found'));
            }
        }
        return $this->success('success');
    }

    public function show(PostIdRequiredRequest $request)
    {
        RoleHelper::need('post_social_show');

        $post = Post::where('user_id', Auth::id())->find($request->__get('post_id'));
        if (!$post) {
            return $this->fail(trans('global.not_found'));
        } else {
            return $this->success('', ['post' => new PostResource($post)]);
        }
    }

    public function update(PostIdRequiredRequest $request)
    {
        // TODO: check post update
        RoleHelper::need('post_social_update');

        $post = Post::where('user_id', Auth::id())->find($request->__get('post_id'));
        if (!$post) {
            return $this->fail(trans('global.not_found'));
        } else {
            return $this->success('', ['updated' => $post->update($request->all())]);
        }
    }

    public function delete(PostIdRequiredRequest $request)
    {
        RoleHelper::need('post_social_delete');

        $post = Post::where('user_id', Auth::id())->find($request->__get('post_id'));
        if (!$post) {
            return $this->fail(trans('global.not_found'));
        } else {
            return $this->success('', ['deleted' => $post->delete($request->__get('post_id'))]);
        }
    }

    public function calendar()
    {
        RoleHelper::need('post_social_show');

        $cal = Post::select(
            'posts.id',
            'posts.account_id',
            'posts.post_caption',
            'posts.post_data',
            'posts.created_at',
            'posts.updated_at',
            'account_manager.name as ac_name',
            'account_manager.account_url',
            'account_manager.social_network_id',
            'social_networks.name',
            'social_networks.icon',
            'social_networks.color')
            ->join('account_manager', 'posts.account_id', '=', 'account_manager.id')
            ->join('social_networks', 'account_manager.social_network_id', '=', 'social_networks.id')
            ->where('posts.user_id', '=', Auth::id())
            ->get();

        $data = [
            'calendar' => CalendarResource::collection($cal),
        ];
        return $this->success('', $data);
    }

    public function liveStreamPost($accounts, $post_data){
        $post_file = $post_data["post_data"]["files"][0];
        $file = FileManager::where('user_id', Auth::id())->find($post_file);
        if($file->type != 'video'){
            return;
        }

        $post_schedule = $post_data['post_schedule'];

        if ($post_schedule){
            /// TODO: not implemented scheduled livestream yet
            return;
        }

        $file_url = urldecode(ImageToken::getToken($file->url));
        $stream_url_arr = [];
        foreach ($accounts as $account){
            $account = (new AccountManager)->where([['user_id', Auth::id()], ['id', $account]])->first();
            $post_data['account_id'] = $account->id;
            $post_data['post_status'] = 0;
            $post = Post::create($post_data);
            $params = [];
            switch ($account->__get('social_network_id')){
                case 1: //facebook
                    $endpoint = '/' . $account->__get('profile_id') . '/live_videos';
                    if(!$post_schedule){
                        $params['status'] = 'LIVE_NOW';
                    }
                    else{
                        /// TODO: not implemented scheduled livestream yet
                        $params['status'] = 'SCHEDULED_UNPUBLISHED';
                    }
                    $params['title'] = $post_data['post_title'];
                    $params['description'] = $post_data['post_caption'];
                    $response = $this->facebook_sdk->post(
                        $endpoint,
                        $params,
                        $account->__get('auth_token')
                    );
                    if($response->getHttpStatusCode() == 200){
                        $response_json = $response->getDecodedBody();
                        $stream_id = $response_json['id'];
                        $stream_url = $response_json['secure_stream_url'];
                        $post->post_id = $stream_id;
                        array_push($stream_url_arr, $stream_url);
                        //StartLiveStream::dispatch($stream_url, $file_url, $post);
                    }
                    else{
                        throw new \Exception($response->getBody());
                    }
                    break;

                default: break;
            }
        }
        StartLiveStream::dispatch($stream_url_arr, $file_url, $post);
    }

}
