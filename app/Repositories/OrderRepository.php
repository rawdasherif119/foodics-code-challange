<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;


class OrderRepository extends BaseRepository
{
    protected $filter = null;

    /**
     *  Return the model
     */
    public function model(): string
    {
        return Order::class;
    }


    /**
     * @param Mixed $data
     * @param array $data
     */
    protected function createManytoManyRelations($model, $data): void
    {
        $this->attach($model->products(), $data);
    }
}
