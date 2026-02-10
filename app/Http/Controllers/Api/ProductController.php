<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\PaginationResource;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\ShowProductRequest;
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
        $perPage = $request->get("page", 20);

        $products = $this->productRepository->getAll($search, $name, $sku, $perPage);

        return new PaginationResource($products, ProductResource::class);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepository->create($request->all());

        return new ProductResource($product);
    }

    public function show(ShowProductRequest $request, string $id)
    {
        $product = $this->productRepository->find($id);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $product = $this->productRepository->update($id, $request->all());

        return new ProductResource($product);
    }
}
