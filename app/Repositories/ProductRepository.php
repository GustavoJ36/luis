<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll($search = null, $name = null, $sku = null, $perPage = 20)
    {
        $query = Product::orderBy("id", "desc");

        if ($name) {
            $query->where("name", "like", "%" . $name . "%");
        }

        if ($sku) {
            $query->where("sku", "like", "%" . $sku . "%");
        }

        return $query->paginate($perPage);
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
