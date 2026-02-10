<?php

namespace App\Repositories;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository
{
    /**
     * Get top selling products within a date range.
     *
     * @param string $from
     * @param string $to
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopProducts(string $from, string $to, int $limit = 5): Collection
    {
        return OrderItem::select(
                'product_id', 
                DB::raw('SUM(quantity) as total_sold')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('product_id')
            ->orderByRaw('total_sold DESC')
            ->limit($limit)
            ->with('product')
            ->get();
    }
}
