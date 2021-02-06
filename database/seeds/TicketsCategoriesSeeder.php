<?php

use App\Model\Ticket\TicketCategories;
use Illuminate\Database\Seeder;

class TicketsCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'category_name' => 'Internet',
            ],
            [
                'id' => 2,
                'category_name' => 'Program',
            ],
            [
                'id' => 3,
                'category_name' => 'Technical',
            ],
            [
                'id' => 4,
                'category_name' => 'News',
            ],
            [
                'id' => 5,
                'category_name' => 'Sport',
            ],
            [
                'id' => 6,
                'category_name' => 'Publishing',
            ],
            [
                'id' => 7,
                'category_name' => 'Cinema',
            ],
            [
                'id' => 8,
                'category_name' => 'Theatre',
            ],
        ];

        TicketCategories::insert($roles);
    }
}
