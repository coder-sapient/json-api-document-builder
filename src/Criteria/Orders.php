<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Criteria;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Orders implements IteratorAggregate, Countable
{
    /** @var Order[] */
    private array $orders = [];

    /**
     * @param Order ...$orders
     */
    public function __construct(Order ...$orders)
    {
        foreach ($orders as $order) {
            $this->add($order);
        }
    }

    /**
     * @param Order $order
     * @return void
     */
    public function add(Order $order): void
    {
        if (! in_array($order, $this->orders, true)) {
            $this->orders[] = $order;
        }
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * @return int
     */
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
