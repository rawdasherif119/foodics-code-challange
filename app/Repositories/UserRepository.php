<?php

namespace App\Repositories;

use App\Models\User;


class UserRepository extends BaseRepository
{
    protected $filter = null ;

    /**
     *  Return the model
     */
    public function model() :string
    {
        return User::class;
    }
}
