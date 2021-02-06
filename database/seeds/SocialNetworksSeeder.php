<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialNetworksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $social_networks = [
            [
                'id' => 1,
                'name' => 'Facebook',
                'icon' => 'mdi-facebook',
                "color" => "#3B5998"
            ],
            [
                'id' => 2,
                'name' => 'Twitter',
                'icon' => 'mdi-twitter',
                "color" => "#38A1F3"
            ],
            [
                'id' => 3,
                'name' => 'Instagram',
                'icon' => 'mdi-instagram',
                "color" => "#3f729b"
            ],
            [
                'id' => 4,
                'name' => 'LinkedIn',
                'icon' => 'mdi-linkedin',
                "color" => "#2867B2"
            ],
            [
                'id' => 5,
                'name' => 'Pinterest',
                'icon' => 'mdi-pinterest',
                "color" => "#E60023"
            ],
            [
                'id' => 6,
                'name' => 'Telegram',
                'icon' => 'mdi-telegram',
                "color" => "#0088CC"
            ],
            [
                'id' => 7,
                'name' => 'Tumblr',
                'icon' => 'mdi-alpha-t',
                "color" => "#34526f"
            ],
            [
                'id' => 8,
                'name' => 'Reddit',
                'icon' => 'mdi-reddit',
                "color" => "#FF4500"
            ],
            [
                'id' => 9,
                'name' => 'VKontakte',
                'icon' => 'mdi-vk',
                "color" => "#45668e"
            ],
            [
                'id' => 10,
                'name' => 'OK',
                'icon' => 'mdi-odnoklassniki',
                "color" => "#ed812b"
            ],
            [
                'id' => 11,
                'name' => 'Foursquare',
                'icon' => 'mdi-alpha-f',
                "color" => "#F94877"
            ],
            [
                'id' => 12,
                'name' => 'Youtube',
                'icon' => 'mdi-youtube',
                "color" => "#FF0000"
            ],
            [
                'id' => 13,
                'name' => 'Whatsapp',
                'icon' => 'mdi-whatsapp',
                "color" => "#4AC959"
            ],
            [
                'id' => 14,
                'name' => 'Google',
                'icon' => 'mdi-google',
                "color" => "#0F9D58"
            ],
        ];

        DB::table('social_networks')->insert($social_networks);
    }
}
