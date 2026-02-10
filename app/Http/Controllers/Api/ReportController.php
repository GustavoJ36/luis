<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Http\Requests\Report\TopProductReportRequest;

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
     * @param TopProductReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topProducts(TopProductReportRequest $request)
    {

        $from = $request->input('from');
        $to = $request->input('to');
        $limit = $request->input('limit', 5);

        $topProducts = $this->orderRepository->getTopProducts($from, $to, $limit);

        return response()->json($topProducts);
    }
}
