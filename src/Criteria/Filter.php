<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

class Filter
{
    /**
     * @param string $field
     * @param FilterOperator $operator
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private string $field, private FilterOperator $operator, private mixed $value)
    {
        if (empty($field)) {
            throw new InvalidArgumentException('`Field` can not be empty');
        }
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     *
     * @return Filter
     *
     * @throws InvalidArgumentException
     */
    public static function fromValues(string $field, string $operator, mixed $value): self
    {
        return new self($field, new FilterOperator($operator), $value);
    }

    /**
     * @return string
     */
    public function field(): string
    {
        return $this->field;
    }

    /**
     * @return FilterOperator
     */
    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
