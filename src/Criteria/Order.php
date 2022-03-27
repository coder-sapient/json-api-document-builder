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
     * @param string $field
     * @param OrderType $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private string $field, private OrderType $type)
    {
        if (empty($field)) {
            throw new InvalidArgumentException('`field` can not be empty');
        }
    }

    /**
     * @param string $field
     * @param string $type
     *
     * @return Order
     *
     * @throws InvalidArgumentException
     */
    public static function fromValues(string $field, string $type): self
    {
        return new self($field, new OrderType($type));
    }

    /**
     * @return string
     */
    public function field(): string
    {
        return $this->field;
    }

    /**
     * @return OrderType
     */
    public function type(): OrderType
    {
        return $this->type;
    }
}
