<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected OrderRepository $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Test Case 1: No permite crear pedido si no hay stock suficiente (y no descuenta nada).
     */
    public function test_it_does_not_allow_creating_order_if_insufficient_stock()
    {
        // 1. Arrange: Create user and product with limited stock
        $user = User::factory()->create();
        $initialStock = 10;
        $product = Product::factory()->create([
            'stock' => $initialStock,
            'price' => 100
        ]);

        $orderData = [
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => $initialStock + 5 // Trying to order more than available
                ]
            ]
        ];

        // 2. Act & Assert: Expect Exception
        try {
            $this->orderRepository->create($orderData);
            $this->fail('Expected Exception was not thrown for insufficient stock.');
        } catch (Exception $e) {
            $this->assertStringContainsString('Insufficient stock', $e->getMessage());
        }

        // 3. Assert: Stock remains unchanged
        $product->refresh();
        $this->assertEquals($initialStock, $product->stock, 'Stock should not change after failed order attempt.');

        // Assert: No order was created
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    /**
     * Test Case 2: Si todo está OK, crea pedido y descuenta stock correctamente.
     */
    public function test_it_creates_order_and_deducts_stock_correctly_when_sufficient_stock()
    {
        // 1. Arrange: Create user and product with sufficient stock
        $user = User::factory()->create();
        $initialStock = 20;
        $orderQty = 5;
        $product = Product::factory()->create([
            'stock' => $initialStock,
            'price' => 100
        ]);

        $orderData = [
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => $orderQty
                ]
            ]
        ];

        // 2. Act: Create the order
        $order = $this->orderRepository->create($orderData);

        // 3. Assert: Order created
        $this->assertInstanceOf(Order::class, $order);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'total' => $product->price * $orderQty
        ]);

        // Assert: Order items created
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $orderQty
        ]);

        // Assert: Stock deducted correctly
        $product->refresh();
        $expectedStock = $initialStock - $orderQty;
        $this->assertEquals($expectedStock, $product->stock, 'Stock should be deducted correctly.');
    }
}
