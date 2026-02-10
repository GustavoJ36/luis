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
        // Configurar reintentos para deadlocks comunes
        $maxRetries = 3;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                return DB::transaction(function () use ($data) {
                    $order = Order::create([
                        'user_id' => $data['user_id'],
                        'total' => 0,
                    ]);

                    $total = 0;
                    $orderItemsData = [];
                    
                    $productIds = collect($data['items'])->pluck('product_id')->unique();
                    $lockedProducts = Product::whereIn('id', $productIds)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');
                    
                    foreach ($data['items'] as $item) {
                        $productId = $item['product_id'];
                        
                        if (!isset($lockedProducts[$productId])) {
                            throw new Exception("Product ID {$productId} not found.");
                        }
                        
                        $product = $lockedProducts[$productId];
                        
                        if ($product->stock < $item['qty']) {
                            throw new Exception("Insufficient stock for product: {$product->name}");
                        }
                        
                        // Actualización atómica con verificación
                        $affectedRows = Product::where('id', $productId)
                            ->where('stock', '>=', $item['qty'])
                            ->where('stock', '=', $product->stock) // Verificación adicional de concurrencia
                            ->update([
                                'stock' => DB::raw("stock - {$item['qty']}"),
                                'updated_at' => now()
                            ])
                        ;
                        
                        if ($affectedRows === 0) {
                            // El stock cambió entre la lectura y la actualización
                            throw new Exception("Concurrent modification detected for product: {$product->name}. Please retry.");
                        }
                        
                        // Preparar datos para inserción
                        $orderItemsData[] = [
                            'order_id' => $order->id,
                            'product_id' => $productId,
                            'quantity' => $item['qty'],
                            'price_snapshot' => $product->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        $total += $product->price * $item['qty'];
                    }
                    
                    // Inserción masiva de items del pedido
                    if (!empty($orderItemsData)) {
                        OrderItem::insert($orderItemsData);
                    }
                    
                    // Actualizar total del pedido
                    $order->update(['total' => $total]);
                    
                    return $order->load('items.product');
                });
                
            } catch (Exception $e) {
                // Si es deadlock, reintentar
                if (str_contains($e->getMessage(), 'Deadlock') || 
                    str_contains($e->getMessage(), 'Lock wait timeout') ||
                    str_contains($e->getMessage(), 'Concurrent modification')) {
                    
                    $retryCount++;
                    
                    if ($retryCount >= $maxRetries) {
                        throw new Exception("Failed to create order after {$maxRetries} attempts: " . $e->getMessage());
                    }
                    
                    // Esperar exponencialmente antes de reintentar
                    usleep(100 * pow(2, $retryCount)); // 100ms, 200ms, 400ms
                    continue;
                }
                
                // Si no es deadlock, relanzar la excepción
                throw $e;
            }
        }
        
        throw new Exception("Failed to create order after {$maxRetries} retries.");
    }


    /**
     * Get paginated orders.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 10)
    {
        return Order::with(['user', 'items.product'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
        ;
    }

    /**
     * Find an order by ID with details.
     *
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order
    {
        return Order::with(['user', 'items.product'])->find($id);
    }

    /**
     * Get top selling products within a date range.
     *
     * @param string $from
     * @param string $to
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopProducts(string $from, string $to, int $limit = 5)
    {
        return OrderItem::select(
                'product_id', 
                DB::raw('SUM(quantity) as total_sold')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('product_id')
            ->orderByRaw('SUM(quantity) DESC')
            ->limit($limit)
            ->with('product')
            ->get();
    }
}

