<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Models\Order;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Services\IngredientService;
use App\Http\Resources\ErrorResource;
use App\Repositories\OrderRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderService extends BaseService
{
    protected $repo;

    public function __construct(OrderRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param array $data
     */
    public function store($data): void
    {
        try {
            DB::transaction(function () use ($data) {
                $order = $this->createWithRelations($data['products'], auth()->user()->orders());
                $order->with('products.ingredients');
                App(IngredientService::class)->updateTheStockOfTheIngredientsAccordingToAnOrder($order);
            });
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
