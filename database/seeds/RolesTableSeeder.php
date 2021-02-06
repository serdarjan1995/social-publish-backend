<?php

use App\Model\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'SuperAdmin',
            ],
            [
                'id' => 2,
                'name' => 'Admin',
            ],
            [
                'id' => 3,
                'name' => 'Moderator',
            ],
            [
                'id' => 4,
                'name' => 'Agency',
            ],
            [
                'id' => 5,
                'name' => 'User',
            ],
            [
                'id' => 6,
                'name' => 'AgencyUser',
            ],
        ];

        Role::insert($roles);
    }
}
