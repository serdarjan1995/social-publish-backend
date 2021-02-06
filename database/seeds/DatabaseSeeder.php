<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             PermissionsTableSeeder::class,
             RolesTableSeeder::class,
             PermissionRoleTableSeeder::class,
             SocialNetworksSeeder::class,
             UsersTableSeeder::class,
             AccountManagerDummySeeder::class,
             DummyDataSeeder::class,
             NotificationsSeeder::class,
             PaymentTypesSeeder::class,
             FileManagerDummySeeder::class,
             TicketsCategoriesSeeder::class
         ]);
    }
}
