<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Exception;

class OrderController extends Controller
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
       // Implementation for listing orders if needed
    }

    /**
     * Store a newly created order.
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse|OrderResource
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderRepository->create($request->validated());
            return new OrderResource($order);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating order',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
