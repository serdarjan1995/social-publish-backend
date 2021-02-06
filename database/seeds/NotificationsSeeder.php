<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $notifications = [
            [
                'id' => 1,
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'send_id' => 'asdad1231',
                'message' => 'Lorem ipsum dolor sit amet, consectetur.',
                'extra_details' => 'Facebook',
                'status' => 0,
                'read' => 0,
                'type' => 1
            ],
            [
                'id' => 2,
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'send_id' => 'asdad1231',
                'message' => 'At enim est totam? Voluptatem, voluptatum.',
                'extra_details' => '',
                'status' => 0,
                'read' => 0,
                'type' => 2
            ],
            [
                'id' => 3,
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'send_id' => 'asdad1231',
                'message' => 'Aut doloribus molestiae officia sequi?',
                'extra_details' => 'Facebook',
                'status' => 0,
                'read' => 0,
                'type' => 3
            ],
            [
                'id' => 4,
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'send_id' => 'asdad1231',
                'message' => 'Doloribus molestiae officia sequi?',
                'extra_details' => 'Facebook',
                'status' => 0,
                'read' => 0,
                'type' => 4
            ],
            [
                'id' => 5,
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'send_id' => 'asdad1231',
                'message' => 'Aspernatur aut doloribus molestiae officia sequi?',
                'extra_details' => 'Facebook',
                'status' => 0,
                'read' => 0,
                'type' => 5
            ],
        ];

        DB::table('notifications')->insert($notifications);
    }
}
