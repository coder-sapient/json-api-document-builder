<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

final class Order
{
    public function __construct(private string $by, private OrderType $orderType)
    {
        if (empty($by)) {
            throw new InvalidArgumentException('`Order by` can not be empty');
        }
    }

    public static function fromValues(string $by, string $type): self
    {
        return new self($by, new OrderType($type));
    }

    public function by(): string
    {
        return $this->by;
    }

    public function type(): OrderType
    {
        return $this->orderType;
    }
}
