<?php

namespace App\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Model\AccountManager\AccountManager;
use App\Model\FileManager;
use App\Model\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SendTwitterPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $account;
    protected $post_data;

    /**
     * Create a new job instance.
     *
     * @param array $post_data
     * @param AccountManager $account
     *
     * @return void
     */
    public function __construct($account, $post_data)
    {
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
        $this->twitterPost($this->account, $this->post_data);
    }

    public function twitterPost($account, $post_data)
    {
        try {
            $post_data['post_status'] = 0;
            $post = Post::create($post_data);

            $connection = new TwitterOAuth(
                config('services.twitter.client_id'),
                config('services.twitter.client_secret'),
                json_decode($account->auth_token)->oauth_token,
                json_decode($account->auth_token)->oauth_token_secret);

            switch ($post_data['post_type']) {
                case "text":
                    $result = $connection->post("statuses/update", ["status" => $post_data['post_caption']]);
                    break;

                case "link":
                    $result = $connection->post('statuses/update', [
                        "status" => $post_data['post_caption'] . "\n" . $post_data['post_data'],
                    ]);
                    break;

                case "media":
                    $incoming_files = $post_data['post_data']['files'];
                    $file_urls = array();
                    $path_to_be = array();
                    $medias = array();
                    for ($j = 0; $j < count($incoming_files); $j++) {
                        $file_url = (FileManager::select('url')->where('id', $incoming_files[$j])->first())->url;
                        array_push($file_urls, $file_url);
                        preg_match('/(?<=)(.{30})(?=\..)(.*)/', $file_url, $path_to_be);
                        $file = Storage::disk('s3')->get($file_url);
                        file_put_contents(public_path($path_to_be[0]), $file);

                        if (preg_match('/mp4/', public_path($path_to_be[0]))) {
                            $media = $connection->upload
                            ('media/upload', ['media' => $path_to_be[0], 'media_type' => 'video/mp4',
                                'total_bytes' => filesize($path_to_be[0])], true);
                        } else {
                            $media = $connection->upload('media/upload', ['media' => public_path($path_to_be[0])]);
                        }
                        array_push($medias, $media->media_id_string);
                        @unlink($path_to_be[0]);
                    }

                    $parameters = [
                        'status' => $post_data['post_caption'],
                        'media_ids' => implode(',', $medias)
                    ];
                    $result = $connection->post('statuses/update', $parameters);
                    break;
            }

            try {
                $post->post_status = 1;
                $post->post_id = $result->id_str;
                $post->save();
            } catch (\Exception $e) {
                echo $e->getMessage();
                $post->post_status = 2;
                $post->save();
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
