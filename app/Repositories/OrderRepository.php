<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderRepository
{
    /**
     * Create a new order with items, handling stock and concurrency.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'total' => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                // Lock the product row for update to handle concurrency
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new Exception("Product ID {$item['product_id']} not found.");
                }

                if ($product->stock < $item['qty']) {
                    throw new Exception("Insufficient stock for product: {$product->name}");
                }

                $product->stock -= $item['qty'];
                $product->save();

                // Create Order Item
                $orderItem = new OrderItem([
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price_snapshot' => $product->price,
                ]);

                $order->items()->save($orderItem);

                $total += $product->price * $item['qty'];
            }

            // Update order total
            $order->update(['total' => $total]);

            return $order->load('items.product');
        });
    }
}
