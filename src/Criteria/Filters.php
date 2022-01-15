<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Filters implements IteratorAggregate, Countable
{
    /** @var Filter[] */
    private array $filters = [];

    public function __construct(Filter ...$filters)
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    public function add(Filter $filter): void
    {
        if (! in_array($filter, $this->filters, true)) {
            $this->filters[] = $filter;
        }
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function count(): int
    {
        return count($this->filters);
    }

    /**
     * @return ArrayIterator<int, Filter>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->filters);
    }
}
