<?php

namespace App\Repositories;

use App\Models\Ingredient;


class IngredientRepository extends BaseRepository
{
    protected $filter = null ;

    /**
     *  Return the model
     */
    public function model() :string
    {
        return Ingredient::class;
    }
}
