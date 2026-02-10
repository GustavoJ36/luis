<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get top selling products within a date range.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topProducts(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');
        $limit = $request->input('limit', 5);

        $topProducts = $this->orderRepository->getTopProducts($from, $to, $limit);

        return response()->json($topProducts);
    }
}
