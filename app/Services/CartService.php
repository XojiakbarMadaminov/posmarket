<?php

namespace App\Services;

use App\Models\Product;

class CartService
{
    protected string $key = 'pos_cart';

    public function all(): array
    {
        return session($this->key, []);
    }

    public function add(Product $product, int $qty = 1): void
    {
        $items = $this->all();

        if (isset($items[$product->id])) {
            $items[$product->id]['qty'] += $qty;
        } else {
            $items[$product->id] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'qty'   => $qty,
            ];
        }

        session()->put($this->key, $items);
    }

    public function update(int $productId, int $qty): void
    {
        $items = $this->all();
        if (isset($items[$productId])) {
            $items[$productId]['qty'] = max(1, $qty);
            session()->put($this->key, $items);
        }
    }

    public function remove(int $productId): void
    {
        $items = $this->all();
        unset($items[$productId]);
        session()->put($this->key, $items);
    }

    public function clear(): void
    {
        session()->forget($this->key);
    }

    public function totals(): array
    {
        $items  = $this->all();
        $qty    = array_sum(array_column($items, 'qty'));
        $amount = array_sum(array_map(fn ($i) => $i['qty'] * $i['price'], $items));

        return ['qty' => $qty, 'amount' => $amount];
    }
}

