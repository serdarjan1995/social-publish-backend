<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // add some api_keys
        $api_keys = [
            [
                'id' => 1,
                'social_network_id' => 1,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => '{"api_version":"v8.0"}'
            ],[
                'id' => 2,
                'social_network_id' => 2,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 3,
                'social_network_id' => 3,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 4,
                'social_network_id' => 4,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 5,
                'social_network_id' => 5,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 6,
                'social_network_id' => 6,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 7,
                'social_network_id' => 7,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 8,
                'social_network_id' => 8,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 9,
                'social_network_id' => 9,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 10,
                'social_network_id' => 10,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 11,
                'social_network_id' => 11,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 12,
                'social_network_id' => 12,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 13,
                'social_network_id' => 13,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],[
                'id' => 14,
                'social_network_id' => 14,
                'api_key' => '',
                'api_secret' => '',
                'api_callback_url' => '',
                'extra_settings' => null
            ],
        ];
        DB::table('api_keys')->insert($api_keys);

        $posts = [
            [
                'id' => 'aa22cc0b-2672-1cca-a348-a711eef30f23',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'post_id' => '21532t4e4t42t',
                'account_id' => 1,
                'post_caption' => 'category 1',
                'post_data' => null,
                'post_type' => 'media',
                'post_schedule' => null,
                'post_status' => 0,
            ],
            [
                'id' => '2cae7e52-d70d-4de8-9241-6db6afa816f2',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'post_id' => 'sdgag343g22',
                'account_id' => 1,
                'post_caption' => 'category 1',
                'post_data' => null,
                'post_type' => 'media',
                'post_schedule' => null,
                'post_status' => 0,
            ],
            [
                'id' => '1b23f2f5-aac7-4b8b-85e2-9e81169f967d',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'post_id' => 'adrusrtsjhtr6u4764',
                'account_id' => 1,
                'post_caption' => 'category 1',
                'post_data' => null,
                'post_type' => 'media',
                'post_schedule' => null,
                'post_status' => 0,
            ],
            [
                'id' => '583f0db7-dd72-4eea-ad73-7e5372148fce',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'post_id' => 'trjj568254545y',
                'account_id' => 1,
                'post_caption' => 'category 1',
                'post_data' => null,
                'post_type' => 'media',
                'post_schedule' => null,
                'post_status' => 0,
            ],
            [
                'id' => 'e1eedfec-297f-443b-8612-8c76eeab8f7b',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'post_id' => 'saherj535y3',
                'account_id' => 1,
                'post_caption' => 'category 1',
                'post_data' => null,
                'post_type' => 'media',
                'post_schedule' => null,
                'post_status' => 0,
            ],
        ];
        DB::table('posts')->insert($posts);
    }
}
