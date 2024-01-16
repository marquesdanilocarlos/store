<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductCreated extends Event
{
    public const NAME = 'product.created';

    public function __construct(protected Product $product)
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

}