<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class Orders implements IteratorAggregate, Countable
{
    /** @var Order[] */
    private array $orders = [];

    public function __construct(Order ...$orders)
    {
        foreach ($orders as $order) {
            $this->add($order);
        }
    }

    public function add(Order $order): void
    {
        if (! in_array($order, $this->orders, true)) {
            $this->orders[] = $order;
        }
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function count(): int
    {
        return count($this->orders);
    }

    /**
     * @return ArrayIterator<int, Order>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->orders);
    }
}
