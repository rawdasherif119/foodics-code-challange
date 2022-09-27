<?php

namespace Tests\Traits;

use App\Models\User;

trait Authentication
{

    public function signInAsCustomer()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        return $user;
    }

}
