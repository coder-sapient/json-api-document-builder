<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

final class OrderType
{
    public const ASC = 'asc';
    public const DESC = 'desc';

    private const ORDER_TYPES = [
        self::ASC,
        self::DESC,
    ];

    public function __construct(private string $type = self::ASC)
    {
        if (! in_array($type, self::ORDER_TYPES, true)) {
            throw new InvalidArgumentException("Invalid order type [{$type}]");
        }
    }

    public function value(): string
    {
        return $this->type;
    }

    public function isAsc(): bool
    {
        return self::ASC === $this->value();
    }
}
