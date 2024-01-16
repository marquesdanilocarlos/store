<?php

namespace App\EventSubscriber;

use App\Event\ProductCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.created' => 'onProductCreated',
        ];
    }

    public function onProductCreated(ProductCreated $event): void
    {
        $product = $event->getProduct();

        $this->logger->info("Este é um evento: O produto {$product->getName()} foi inserido na base!");
    }
}
