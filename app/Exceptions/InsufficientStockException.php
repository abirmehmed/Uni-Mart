<?php

namespace App\Exceptions;

use App\Models\Product;
use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(public Product $product, public int $requested)
    {
        parent::__construct(
            "Cannot sell {$requested} of \"{$product->name}\" — only {$product->stock_quantity} in stock."
        );
    }
}
