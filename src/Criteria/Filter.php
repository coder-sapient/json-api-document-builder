<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

final class Filter
{
    public function __construct(private string $field, private FilterOperator $operator, private mixed $value)
    {
        if (empty($field)) {
            throw new InvalidArgumentException('`Field` can not be empty');
        }
    }

    public static function fromValues(string $field, string $operator, mixed $value, ): self
    {
        return new self($field, new FilterOperator($operator), $value);
    }

    public function field(): string
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
