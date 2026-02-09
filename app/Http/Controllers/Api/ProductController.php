<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\PaginationResource;
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
        $name = $request->get("name");
        $sku = $request->get("sku");

        $products = $this->productRepository->getAll($search, $name, $sku);

        return new PaginationResource($products, ProductResource::class);
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
