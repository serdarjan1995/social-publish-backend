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
use App\Http\Controllers\SocialMediaApi\Linkedin\Post as Linkedin;

class SendLinkedinPost implements ShouldQueue
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
        $this->linkedinPost($this->account, $this->post_data);
    }

    public function linkedinPost($account, $post_data)
    {
        (new \App\Http\Controllers\SocialMediaApi\Linkedin\Post)->sendPost($account, $post_data);
    }
}
