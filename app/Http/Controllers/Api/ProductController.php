<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Product\ProductResource;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request)
    {
        $search = $request->get("search");

        $products = Product::orderBy("id", "desc")
            ->where(function($query) use ($search) {
                if ($search) {
                    $query->where("name", "like", "%" . $search . "%");
                }
            })
            ->paginate(20)
        ;

        return response()->json([
            "total" => $products->total(),
            "products" => ProductResource::collection($products),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());

        return response()->json([
            "product" => ProductResource::make($product),
        ]);
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            "product" => ProductResource::make($product),
        ]);
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json([
            "product" => ProductResource::make($product),
        ]);
    }
}
