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

class Filters implements IteratorAggregate, Countable
{
    /** @var Filter[] */
    private array $filters = [];

    /**
     * @param Filter ...$filters
     */
    public function __construct(Filter ...$filters)
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    /**
     * @param Filter $filter
     *
     * @return void
     */
    public function add(Filter $filter): void
    {
        if (! in_array($filter, $this->filters, true)) {
            $this->filters[] = $filter;
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
