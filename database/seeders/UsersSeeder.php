<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'customerTest1@gmail.com'],
            [
                'name'     => 'customer Test1',
                'email'    => 'customerTest1@gmail.com',
                'password' => '12345678',
            ]
        );
    }
}
