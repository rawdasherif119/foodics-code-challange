<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Response;
use Tests\Traits\Authentication;
use Illuminate\Support\Facades\Mail;
use App\Mail\IngredientReachedBelowItsHalflevel;

class OrderTest extends TestCase
{
    use Authentication;

    private $orderPath = '/api/orders';

    /**
     * @test
     */
    public function order_Added_successfully_and_stock_correctly_updated()
    {
        $user = $this->signInAsCustomer();
        $data = [
            'products' => [
                [
                    'product_id' => Product::first()->id,
                    'quantity' => rand(1, 5)
                ], [
                    'product_id' => Product::skip(1)->first()->id,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];

        $this->post($this->orderPath, $data)->assertOk();
        /** Order has been added*/
        $this->assertDatabaseHas('orders', ['user_id' => $user->id])->assertEquals(1, Order::count());
        $this->assertDatabaseCount('order_product', sizeof($data['products']));
        /******************************** */

        $order = Order::first();

        $ingredients = [];
        foreach ($data['products'] as $product) {
            /** Order Details have been added */
            $this->assertDatabaseHas('order_product', array_merge($product, ['order_id'    => $order->id]));
            /******************************** */

            // Calculate total requested grams for each ingredient in product in an order  
            $productIngredients = Product::find($product['product_id'])->ingredients;
            foreach ($productIngredients as $ingredient) {
                $ingredients[$ingredient->id] = array_key_exists($ingredient->id, $ingredients) ?
                    $ingredients[$ingredient->id] + ($ingredient->pivot->amount * $product['quantity']) :
                    $ingredient->pivot->amount * $product['quantity'];
            }
        }

        foreach ($ingredients as $id => $newAmount) {
            /** Ingrediants have been updated  */
            $this->assertDatabaseHas('ingredients', [
                'id' => $id,
                'current_amount' => (Ingredient::find($id)->max_amount) - $newAmount
            ]);
        }
    }

    /**
     * @test
     */
    public function reject_order_if_not_enough_ingredients_in_stock()
    {
        $user = $this->signInAsCustomer();
        $product = Product::first();
        $product->ingredients->first()->update(['current_amount' => 0]);

        $data = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id])->assertEquals(0, Order::count());
    }

    /**
     * @test
     */
    public function product_id_required_in_order_request()
    {
        $this->signInAsCustomer();
        $data = [
            'products' => [
                [
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnprocessable();
    }

    /**
     * @test
     */
    public function quantity_required_in_order_request()
    {
        $this->signInAsCustomer();
        $product = Product::first();
        $data = [
            'products' => [
                [
                    'product_id' => $product->id,
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnprocessable();
    }

    /**
     * @test
     */
    public function product_id_in_order_request_must_exist_in_order_table()
    {
        $this->signInAsCustomer();
        $productsCount = Product::count();
        $data = [
            'products' => [
                [
                    'product_id' => $productsCount + 1,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnprocessable();
    }

    /**
     * @test
     */
    public function customer_must_authorized_to_request_an_order()
    {
        $product = Product::first();
        $data = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];
        $this->post($this->orderPath, $data)->assertUnauthorized();
    }

    /**
     * @test
     */
    public function send_email_if_any_ingredient_reach_its_half_level_or_below()
    {
        $user = $this->signInAsCustomer();
        $product = Product::first();
        $ingredient = $product->ingredients->first();
        $ingredient2 = $product->ingredients->skip(1)->first();
        $ingredient->update(['current_amount' => $ingredient->current_amount * 0.5]);
        $data = [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5)
                ]
            ]
        ];

        $this->post($this->orderPath, $data)->assertOk();
        /** Order has been added*/
        $this->assertDatabaseHas('orders', ['user_id' => $user->id])->assertEquals(1, Order::count());
        $this->assertDatabaseCount('order_product', sizeof($data['products']));
        /******************************** */

        $order = Order::first();
        foreach ($data['products'] as $product) {
            $this->assertDatabaseHas('order_product', array_merge($product, ['order_id'    => $order->id]));
        }

        Mail::fake();
        Mail::to(['merchant1@email.com', 'merchant2@gmail'])
            ->send(new IngredientReachedBelowItsHalflevel($ingredient));
        Mail::assertSent(IngredientReachedBelowItsHalflevel::class);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'alert_email_sent' => true
        ]);
    }
}
