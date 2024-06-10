<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSearchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_search')->insert([
            [
                'product_id' => 67964,
                'search_text' =>
                    'Vanjski retrovizor AUTO KELLY 18.28.213, 18.28.213, RETROVIZOR, LKQ, 5894413, 5896314, 0000005894413, 10744801175480330900165894413',
            ],
        ]);

        $this->call(DeliveryDeadlinesSeeder::class);
    }
}
