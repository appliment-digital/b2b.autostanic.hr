<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarrentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warrents')->insert([
            ['description' => '30 dana'],
            ['description' => '60 dana'],
            ['description' => '90 dana'],
            ['description' => '6 mjeseci'],
            ['description' => '12 mjeseci'],
            ['description' => '24 mjeseci'],
            ['description' => '36 mjeseci'],
        ]);

        $this->call(DeliveryDeadlinesSeeder::class);
    }
}
