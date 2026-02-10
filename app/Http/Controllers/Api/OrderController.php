<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\ShowOrderRequest;
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

    /**
     * Display a listing of orders (paginated).
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $orders = $this->orderRepository->getAll();
        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order.
     *
     * @param int $id
     * @return OrderResource|JsonResponse
     */
    public function show(ShowOrderRequest $request, $id)
    {
        $order = $this->orderRepository->findById($id);

        return new OrderResource($order);
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
