<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(IndexProductRequest $request)
    {
        $search = $request->get("search");

        $products = $this->productRepository->getAll($search);

        return response()->json([
            "total" => $products->total(),
            "per_page" => $products->perPage(),
            "current_page" => $products->currentPage(),
            "last_page" => $products->lastPage(),
            "current_page_url" => $products->url($products->currentPage()),
            // "first_page_url" => $products->url(1),
            // "last_page_url" => $products->url($products->lastPage()),
            // "next_page_url" => $products->nextPageUrl(),
            // "prev_page_url" => $products->previousPageUrl(),
            "path" => $products->path(),
            "from" => $products->firstItem(),
            "to" => $products->lastItem(),
            "data" => ProductResource::collection($products)->resolve(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepository->create($request->all());

        return response()->json([
            "product" => ProductResource::make($product),
        ]);
    }

    public function show(string $id)
    {
        $product = $this->productRepository->find($id);

        return response()->json([
            "product" => ProductResource::make($product),
        ]);
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $product = $this->productRepository->update($id, $request->all());

        return response()->json([
            "product" => ProductResource::make($product),
        ]);
    }
}
