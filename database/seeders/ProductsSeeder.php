<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**Ingredients */
        $beef    = Ingredient::Where('name', 'Beef')->first();
        $cheese  = Ingredient::Where('name', 'Cheese')->first();
        $onion   = Ingredient::Where('name', 'Onion')->first();
        $chicken = Ingredient::Where('name', 'Chicken')->first();

        /** Products */
        $burger = Product::updateOrCreate(['name' => 'Burger']);
        $burger->ingredients()->sync([
            $beef->id   => ['amount' => 150],
            $cheese->id => ['amount' => 30],
            $onion->id  => ['amount' => 20]
        ]);

        $chickenSandwich = Product::updateOrCreate(['name' => 'Chicken sandwich']);
        $chickenSandwich->ingredients()->sync([
            $chicken->id   => ['amount' => 200],
            $cheese->id    => ['amount' => 30],
            $onion->id     => ['amount' => 20]
        ]);
    }
}
