<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Appliment',
            'last_name' => 'Admin',
            'email' => 'info@appliment.eu',
            'password' =>  Hash::make('admin#2024'),
            'bitrix_company_id' => 0
        ])->assignRole('admin');
    }
}
