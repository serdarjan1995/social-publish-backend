<?php

namespace App\Jobs;

require(app_path('Http/libraries/vendor/autoload.php'));

use App\Helpers\DownloadHelper;
use App\Model\AccountManager\AccountManager;
use App\Model\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use InstagramAPI\Constants;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Instagram;
use InstagramAPI\Media\InstagramMedia;
use InstagramAPI\Media\Photo\InstagramPhoto;
use InstagramAPI\Media\Video\FFmpeg;
use InstagramAPI\Media\Video\InstagramVideo;
use InstagramAPI\Utils;
use Exception;

/**
 * Class SendInstagramPost
 * @package App\Jobs
 */
class SendInstagramPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Instagram
     */
    protected $post_data;
    protected $account;

    /**
     * SendInstagramPost constructor.
     * @param $post_data
     * @param $account
     */
    public function __construct($post_data, $account)
    {
        $this->post_data = $post_data;
        $this->account = $account;
    }


    public function handle()
    {
        $this->InstagramPost($this->post_data);
    }

    private function instagramPost($post_data, $proxy = null)
    {
        try {
            $ig = $this->login();
            if (isset($proxy)) {
                $ig->setProxy($proxy);
            }
            $post_data['post_status'] = 0;
            $post = Post::create($post_data);
            $params = [];
            $resp = '';
            try {
                $download = DownloadHelper::download($this->post_data["post_data"]["files"], $this->post_data["user_id"]);

                switch ($post_data['post_type']) {
                    case 'igtv':
                        if ($this->check_post_video()) {
                            $params['title'] = $post_data['post_caption'];
                            $params['caption'] = $post_data['post_caption'];
                            $video = new InstagramVideo(public_path($download[0]), [
                                "targetFeed" => Constants::FEED_STORY
                            ]);

                            $resp = $ig->tv->uploadVideo($video->getFile(), $params);
                        }
                        break;
                    case 'story':
                        if ($this->is_image(public_path($download[0]))) {
                            $img = new InstagramPhoto(public_path($download[0]), [
                                "targetFeed" => Constants::FEED_STORY,
                                "operation" => InstagramMedia::CROP
                            ]);

                            $resp = $ig->story->uploadPhoto($img->getFile());
                        } else {
                            $video = new InstagramVideo(public_path($download[0]), [
                                "targetFeed" => Constants::FEED_STORY
                            ]);
                            $resp = $ig->story->uploadVideo($video->getFile());
                        }
                        break;
                    case 'media':
                        $params['caption'] = $post_data['post_caption'];
                        try {
                            if ($this->is_image(public_path($download[0]))) {
                                $img = new InstagramPhoto(public_path($download[0]), [
                                    "targetFeed" => Constants::FEED_TIMELINE,
                                    "operation" => InstagramMedia::CROP
                                ]);
                                $resp = $ig->timeline->uploadPhoto($img->getFile(), $params);
                            } else {
                                if ($this->check_post_video()) {
                                    $video = new InstagramVideo(public_path($download[0]), [
                                        "targetFeed" => Constants::FEED_TIMELINE,
                                        "operation" => InstagramMedia::CROP
                                    ]);
                                    $resp = $ig->timeline->uploadVideo($video->getFile(), $params);
                                }
                            }
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                        break;
                    case 'carousel':
                        $params['caption'] = $post_data['post_caption'];
                        $carousels = [];
                        $medias = array_chunk($download, 10);
                        foreach ($download as $key => $media) {
                            $medias[$key] = $media;
                            if ($this->is_image(public_path($media))) {
                                try {
                                    $media = new InstagramPhoto(public_path($media), [
                                        "targetFeed" => Constants::FEED_TIMELINE_ALBUM,
                                        "operation" => InstagramMedia::CROP
                                    ]);
                                    $carousels[] = [
                                        'type' => 'photo',
                                        'file' => $media->getFile()
                                    ];
                                } catch (Exception $e) {
                                    $carousels[] = [
                                        'type' => 'Render Image Error',
                                        'message' => $e->getMessage()
                                    ];
                                }
                            } else {
                                try {
                                    $media = new InstagramVideo(public_path($media), [
                                        "targetFeed" => Constants::FEED_TIMELINE_ALBUM
                                    ]);
                                    $carousels[] = [
                                        'type' => 'video',
                                        'file' => $media->getFile()
                                    ];
                                } catch (Exception $e) {
                                    $carousels[] = [
                                        'type' => 'Render Image Error',
                                        'message' => $e->getMessage()
                                    ];
                                }
                            }

                        }

                        $resp = $ig->timeline->uploadAlbum($carousels, $params);
                        break;
                }


                $post->post_status = 1;
                $post->post_id = json_decode($resp, true)['media']['pk'];
                $post->save();


            } catch (InstagramException $e) {
                $post->post_status = 2;
                $post->save();
                throw new InstagramException($e->getMessage());
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    private function location(string $latitude, string $longitude, string $query)
    {
        try {
            $ig = $this->login();
            $locations = $ig->location->search($latitude, $longitude, $query);
            $locations = $locations->getVenues();
            return $locations[1];
        } catch (InstagramException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function login()
    {
        try {
            $data = AccountManager::findOrFail($this->post_data['account_id']);
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
            $ig = new Instagram(false, false,
                [
                    'storage' => env('DB_CONNECTION'),
                    'dbhost' => env('DB_HOST'),
                    'dbname' => env('DB_DATABASE'),
                    'dbusername' => env('DB_USERNAME'),
                    'dbpassword' => env('DB_PASSWORD'),
                    'dbtablename' => 'session_instagram'
                ]);

            $ig->login($data->username, Crypt::decryptString($data->auth_token));
            return $ig;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function is_image($media)
    {
        if (is_array(getimagesize(($media)))) {
            return true;
        } else {
            return false;
        }
    }

    public function check_post_video()
    {

        Utils::$ffmpegBin = "/usr/bin/ffmpeg";
        Utils::$ffprobeBin = "/usr/bin/ffprobe";

        if (Utils::checkFFPROBE()) {
            try {
                FFmpeg::factory();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
}
