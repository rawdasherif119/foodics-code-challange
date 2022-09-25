<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            ['name' => 'Beef', 'max_amount'   => 20000, 'current_amount' => 20000],
            ['name' => 'Cheese', 'max_amount' => 5000, 'current_amount'  => 5000],
            ['name' => 'Onion', 'max_amount'  => 1000, 'current_amount'  => 1000]
        ];

        foreach ($ingredients as $ingredient)
            Ingredient::updateOrCreate(['name' => $ingredient['name']], $ingredient);
    }
}
