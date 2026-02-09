<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll($search = null)
    {
        return Product::orderBy("id", "desc")
            ->where(function($query) use ($search) {
                if ($search) {
                    $query->where("name", "like", "%" . $search . "%");
                }
            })
            ->paginate(20);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function find($id)
    {
        return Product::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return $product;
    }
}
