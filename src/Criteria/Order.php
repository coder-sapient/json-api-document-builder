<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

class Order
{
    /**
     * @param string $by
     * @param OrderType $orderType
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private string $by, private OrderType $orderType)
    {
        if (empty($by)) {
            throw new InvalidArgumentException('`Order by` can not be empty');
        }
    }

    /**
     * @param string $by
     * @param string $type
     *
     * @return Order
     *
     * @throws InvalidArgumentException
     */
    public static function fromValues(string $by, string $type): self
    {
        return new self($by, new OrderType($type));
    }

    /**
     * @return string
     */
    public function by(): string
    {
        return $this->by;
    }

    /**
     * @return OrderType
     */
    public function type(): OrderType
    {
        return $this->orderType;
    }
}
