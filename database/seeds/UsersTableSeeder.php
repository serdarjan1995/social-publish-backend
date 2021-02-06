<?php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(){
        $users = [
            [
                'id' => 'dd14aa6f-2632-4acb-a4f8-c790eef30f50',
                'name' => 'Admin',
                'surname' => 'Dev',
                'default_lang' => 'en',
                'status' => 1,
                'phone_number' => '905551110011',
                'phone_verified' => 1,
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'email_verified_at' => '2020-12-03 10:59:30',
                'email_verified' => 1,
                'created_at' => '2020-12-03 10:59:30',
                'profile_image' => null
            ],
            [
                'id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'Test',
                'surname' => 'User',
                'default_lang' => 'en',
                'status' => 1,
                'phone_number' => '905551110011',
                'phone_verified' => 1,
                'email' => 'test@test.com',
                'password' => Hash::make('password'),
                'email_verified_at' => '2020-12-03 10:59:30',
                'email_verified' => 1,
                'created_at' => '2020-12-03 10:59:30',
                'profile_image' => null
            ],
        ];
        DB::table('users')->insert($users);

        //define user roles
        $user_roles = [
            [
                'user_id' => 'dd14aa6f-2632-4acb-a4f8-c790eef30f50',
                'role_id' => '1'
            ],
            [
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'role_id' => '5'
            ],
        ];
        DB::table('user_roles')->insert($user_roles);
    }
}
