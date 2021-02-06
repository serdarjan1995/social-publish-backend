<?php

use App\Model\AccountManager\AccountManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountManagerDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // add account category
        $account_categories = [
            [
                'id' => 1,
                'social_network_id' => 1,
                'category' => 'profile',
            ],
            [
                'id' => 2,
                'social_network_id' => 1,
                'category' => 'page',
            ],
            [
                'id' => 3,
                'social_network_id' => 1,
                'category' => 'group',
            ],
            [
                'id' => 4,
                'social_network_id' => 2,
                'category' => 'profile',
            ],
            [
                'id' => 5,
                'social_network_id' => 3,
                'category' => 'profile',
            ],
            [
                'id' => 6,
                'social_network_id' => 3,
                'category' => 'business',
            ],
            [
                'id' => 7,
                'social_network_id' => 4,
                'category' => 'profile',
            ],
            [
                'id' => 8,
                'social_network_id' => 4,
                'category' => 'company',
            ],
            [
                'id' => 9,
                'social_network_id' => 5,
                'category' => 'profile',
            ],
            [
                'id' => 10,
                'social_network_id' => 6,
                'category' => 'channel',
            ],
            [
                'id' => 11,
                'social_network_id' => 6,
                'category' => 'group',
            ],
            [
                'id' => 12,
                'social_network_id' => 7,
                'category' => 'blog',
            ],
            [
                'id' => 13,
                'social_network_id' => 8,
                'category' => 'profile',
            ],
            [
                'id' => 14,
                'social_network_id' => 9,
                'category' => 'profile',
            ],
            [
                'id' => 15,
                'social_network_id' => 9,
                'category' => 'page',
            ],
            [
                'id' => 16,
                'social_network_id' => 9,
                'category' => 'group',
            ],
            [
                'id' => 17,
                'social_network_id' => 10,
                'category' => 'group',
            ],
            [
                'id' => 18,
                'social_network_id' => 11,
                'category' => 'checkin',
            ],
            [
                'id' => 19,
                'social_network_id' => 12,
                'category' => 'channel',
            ],
            [
                'id' => 20,
                'social_network_id' => 13,
                'category' => 'group',
            ],
            [
                'id' => 21,
                'social_network_id' => 14,
                'category' => 'profile',
            ],
        ];
        DB::table('account_category')->insert($account_categories);


        // add account add link
        $add_links = [
            [
                'category_id' => 1,
                'type' => 'oauth',
                'uri' => 'https://facebook.com/profile',
            ],
            [
                'category_id' => 2,
                'type' => 'oauth',
                'uri' => 'https://facebook.com/page',
            ],
            [
                'category_id' => 3,
                'type' => 'oauth',
                'uri' => 'https://facebook.com/group',
            ],
            [
                'category_id' => 4,
                'type' => 'oauth',
                'uri' => 'https://twitter.com/profile',
            ],
            [
                'category_id' => 5,
                'type' => 'login',
                'uri' => 'https://instagram.com/',
            ],
            [
                'category_id' => 6,
                'type' => 'oauth',
                'uri' => 'https://instagram.com/',
            ],
            [
                'category_id' => 7,
                'type' => 'oauth',
                'uri' => 'https://linkedin.com/profile',
            ],
            [
                'category_id' => 8,
                'type' => 'oauth',
                'uri' => 'https://linkedin.com/company',
            ],
            [
                'category_id' => 9,
                'type' => 'oauth',
                'uri' => 'https://pinterest.com/profile',
            ],
            [
                'category_id' => 10,
                'type' => 'oauth',
                'uri' => 'https://telegram.com/channel',
            ],
            [
                'category_id' => 11,
                'type' => 'oauth',
                'uri' => 'https://telegram.com/group',
            ],
            [
                'category_id' => 12,
                'type' => 'oauth',
                'uri' => 'https://tumblr.com/profile',
            ],
            [
                'category_id' => 13,
                'type' => 'oauth',
                'uri' => 'https://reddit.com/profile',
            ],
            [
                'category_id' => 14,
                'type' => 'oauth',
                'uri' => 'https://vk.com/profile',
            ],
            [
                'category_id' => 15,
                'type' => 'oauth',
                'uri' => 'https://vk.com/profile',
            ],
            [
                'category_id' => 16,
                'type' => 'oauth',
                'uri' => 'https://vk.com/profile',
            ],
            [
                'category_id' => 17,
                'type' => 'oauth',
                'uri' => 'https://ok.com/profile',
            ],
            [
                'category_id' => 18,
                'type' => 'oauth',
                'uri' => 'https://fourquare.com/profile',
            ],
            [
                'category_id' => 19,
                'type' => 'oauth',
                'uri' => 'https://youtube.com/channel',
            ],
            [
                'category_id' => 20,
                'type' => 'oauth',
                'uri' => 'https://whatsapp.com/group',
            ],
            [
                'category_id' => 21,
                'type' => 'oauth',
                'uri' => 'https://google.com/business_profile',
            ],
        ];
        DB::table('account_add_links')->insert($add_links);

        // add some accounts
        $accounts = [
            [
                'id' => 1,
                'social_network_id' => 1,
                'status' => 1,
                'name' => 'Jennifer Lopez',
                'username' => '@jenniferlopez',
                'profile_id' => '32641461471461',
                'category_id' => 1,
                'login_type' => 'test',
                'avatar_url' => 'https://cdn4.iconfinder.com/data/icons/avatars-xmas-giveaway/128/girl_female_woman_avatar-512.png',
                'account_url' => 'https://www.facebook.com/jenniferlopez/',
                'data' => '{"test":"test","a":"A","b":"B"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 2,
                'social_network_id' => 1,
                'status' => 1,
                'name' => 'Pitbull Music',
                'username' => '@pitbull',
                'profile_id' => '7426234627353',
                'category_id' => 1,
                'login_type' => 'test',
                'avatar_url' => 'https://image.flaticon.com/icons/svg/147/147144.svg',
                'account_url' => 'https://www.facebook.com/pitbull/',
                'data' => '{"test":"test2","a":"AA","b":"BB"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 6,
                'social_network_id' => 1,
                'status' => 1,
                'name' => 'Sustainability in the Water Sector',
                'username' => '423511728446075',
                'profile_id' => '423511728446075',
                'category_id' => 3,
                'login_type' => 'test',
                'avatar_url' => 'https://image.flaticon.com/icons/svg/554/554744.svg',
                'account_url' => 'https://www.facebook.com/groups/423511728446075/',
                'data' => '{"test":"EEE","a":"CC","b":"DD"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 7,
                'social_network_id' => 1,
                'status' => 1,
                'name' => 'Will Smith',
                'username' => '92304305160',
                'profile_id' => '92304305160',
                'category_id' => 2,
                'login_type' => 'test',
                'avatar_url' => 'https://scontent.fsaw3-1.fna.fbcdn.net/v/t1.0-1/p960x960/52719266_10161398655985161_4737563461104435200_o.png?_nc_cat=1&_nc_sid=dbb9e7&_nc_ohc=hnj1g40FjXsAX--146i&_nc_ht=scontent.fsaw3-1.fna&oh=07860768d6e9ab8608abd2dc127e2a14&oe=5F50C062',
                'account_url' => 'https://www.facebook.com/Will-Smith-92304305160/',
                'data' => '{"test":"facebook","a":"pages","b":"will"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 8,
                'social_network_id' => 6,
                'status' => 1,
                'name' => 'LastFM',
                'username' => '@lastfmrobot',
                'profile_id' => '1573576566344',
                'category_id' => 10,
                'login_type' => 'test',
                'avatar_url' => 'https://telegramchannels.me/storage/media-logo/1901/lastfmrobot.jpg',
                'account_url' => 'https://telegramchannels.me/bots/lastfmrobot',
                'data' => '{"test":"telegram","a":"bot","b":"channel"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 9,
                'social_network_id' => 4,
                'status' => 1,
                'name' => 'Accenture',
                'username' => '@accenture',
                'profile_id' => '9876543897654',
                'category_id' => 8,
                'login_type' => 'test',
                'avatar_url' => 'https://media-exp1.licdn.com/dms/image/C4D0BAQG9BHOrEo9JBQ/company-logo_200_200/0?e=2159024400&v=beta&t=GJUnDZXj5Zf2Y0ceCfqfTNH5ReHsxRFJJVJDx50anuI',
                'account_url' => 'https://www.linkedin.com/company/accenture',
                'data' => '{"test":"Accenture","a":"New isn\'t on its way. We\'re applying it right now.","b":"Information Technology and Services"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 3,
                'social_network_id' => 4,
                'name' => 'Microsoft',
                'status' => 1,
                'username' => '@microsoft',
                'profile_id' => '763542236',
                'category_id' => 8,
                'login_type' => 'test',
                'avatar_url' => 'https://media-exp1.licdn.com/dms/image/C4D0BAQEko6uLz7XylA/company-logo_200_200/0?e=2159024400&v=beta&t=a1kve4i0YyusChyNR_Cvvn2vnHNUHhZ4H2rnYCxjQhU',
                'account_url' => 'https://www.linkedin.com/company/microsoft',
                'data' => '{"test":"Microsoft","a":"We’re on a mission to empower every person and every organization on the planet to achieve more.","b":"IComputer Software"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'dd14aa6f-2632-4acb-a4f8-c790eef30f50',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 4,
                'social_network_id' => 4,
                'name' => 'Pavel Durov',
                'status' => 1,
                'username' => '@pavel-durov-80174366',
                'profile_id' => 'pavel-durov-80174366',
                'category_id' => 7,
                'login_type' => 'test',
                'avatar_url' => 'https://static-exp1.licdn.com/sc/h/djzv59yelk5urv2ujlazfyvrk',
                'account_url' => 'https://www.linkedin.com/in/pavel-durov-80174366',
                'data' => '{"test":"132435465","a":"CEO, Founder","b":"Telegram Messenger"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'dd14aa6f-2632-4acb-a4f8-c790eef30f50',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
            [
                'id' => 5,
                'social_network_id' => 5,
                'name' => 'Adam Parfiniewicz',
                'status' => 1,
                'username' => '@aparfiniewicz',
                'profile_id' => 'aparfiniewicz',
                'category_id' => 9,
                'login_type' => 'test',
                'avatar_url' => 'https://i.pinimg.com/280x280_RS/cc/ce/b9/ccceb9cdaf462507c623cc3f74a46018.jpg',
                'account_url' => 'https://www.linkedin.com/in/pavel-durov-80174366',
                'data' => '{"test":"132435465","a":"Alba, bike books, exquisite","b":"Adam Parfiniewicz\'s best boards"}',
                'watermark_details' => '{"watermark_mask": "uploads/33d967ad-af61-4269-afbf-2cab5cff01ed/watermarks/04WIDy28wm4vMQVxPMFif42J17TyvzOsWp8wavvA.png", "watermark_size": "200", "watermark_opacity": "21", "watermark_position": "c"}',
                'user_id' => 'dd14aa6f-2632-4acb-a4f8-c790eef30f50',
                'auth_token' => 'This is test',
                'parent_id' => NULL
            ],
        ];
        AccountManager::insert($accounts);
    }
}
