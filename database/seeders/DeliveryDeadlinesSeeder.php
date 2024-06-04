<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryDeadlinesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_deadlines')->insert([
            ['description' => '1 - 3 dana'],
            ['description' => '5 - 8 dana'],
            ['description' => '5 - 10 dana'],
            ['description' => '10 - 15 dana'],
            [
                'description' =>
                    'Nepoznati rok isporuke (molimo kontaktirati nas)',
            ],
        ]);
    }
}
