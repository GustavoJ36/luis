<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Product\ProductResource;
use App\Http\Requests\Product\IndexProductRequest;

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
}
