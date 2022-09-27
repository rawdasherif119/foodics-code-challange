<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ingredient;
use App\Services\BaseService;
use App\Repositories\IngredientRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class IngredientService extends BaseService
{
    protected $repo;

    public function __construct(IngredientRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Order $order
     */
    public function updateTheStockOfTheIngredientsAccordingToAnOrder($order): void
    {
        $ingredients = $order->ingredients->groupBy('id');
        foreach ($ingredients as $ingredient) {
            $totalGramsForAnIngredient = $this->calculateTotalGramsOfAnIngredientInTheOrder($ingredient);
            $ingredient = $ingredient->first();
            $newAmount = $ingredient->current_amount - $totalGramsForAnIngredient;
            $newAmount >= 0 ?
                $this->update(['current_amount' => $newAmount], $ingredient) :
                throw new BadRequestHttpException(__('errors.not_enough_ingredient', ['ingredient' => $ingredient->name]));
        }
    }

    /**
     * @param Ingredient $ingredient
     */
    public function calculateTotalGramsOfAnIngredientInTheOrder($ingredient): int
    {
        $gramsForOneProduct = array_map(
            function ($amounts, $quantities) {
                return $amounts * $quantities;
            },
            $ingredient->pluck('pivot.amount')->toArray(),
            $ingredient->pluck('pivot.pivotParent.pivot.quantity')->toArray()
        );
        return array_sum($gramsForOneProduct);
    }
}
