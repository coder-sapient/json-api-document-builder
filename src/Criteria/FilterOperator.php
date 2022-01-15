<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

class FilterOperator
{
    public const EQUAL = 'eq';
    public const NOT_EQUAL = 'neq';
    public const GT = 'gt';
    public const LT = 'lt';
    public const GTE = 'gte';
    public const LTE = 'lte';
    public const LIKE = 'like';

    public const OPERATORS = [
        self::EQUAL,
        self::NOT_EQUAL,
        self::GT,
        self::LT,
        self::GTE,
        self::LTE,
        self::LIKE,
    ];

    public function __construct(private string $operator)
    {
        if (! in_array($operator, self::OPERATORS, true)) {
            throw new InvalidArgumentException("Invalid filter operator [{$operator}]");
        }
    }

    public function value(): string
    {
        return $this->operator;
    }

    public function isEqual(string $operator): bool
    {
        return $this->value() === $operator;
    }
}
