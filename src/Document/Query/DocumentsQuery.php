<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Document\Query;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Orders;

class DocumentsQuery extends JsonApiQuery
{
    /** @var Chunk|null */
    private ?Chunk $chunk = null;

    /** @var Orders|null */
    private ?Orders $orders = null;

    /** @var Filters|null */
    private ?Filters $filters = null;

    /**
     * @param string $resourceType
     */
    public function __construct(private string $resourceType)
    {
    }

    /**
     * @return string
     */
    public function resourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @return Chunk
     */
    public function chunk(): Chunk
    {
        return $this->chunk ?? new Chunk();
    }

    /**
     * @return Filters
     */
    public function filters(): Filters
    {
        return $this->filters ?? new Filters();
    }

    /**
     * @return Orders
     */
    public function orders(): Orders
    {
        return $this->orders ?? new Orders();
    }

    /**
     * @param Filters $filters
     *
     * @return $this
     */
    public function setFilters(Filters $filters): self
    {
        $self = clone $this;

        $self->filters = $filters;

        return $self;
    }

    /**
     * @param Orders $orders
     *
     * @return $this
     */
    public function setOrders(Orders $orders): self
    {
        $self = clone $this;

        $self->orders = $orders;

        return $self;
    }

    /**
     * @param Chunk $chunk
     *
     * @return $this
     */
    public function setChunk(Chunk $chunk): self
    {
        $self = clone $this;

        $self->chunk = $chunk;

        return $self;
    }

    /**
     * @return string
     */
    public function hash(): string
    {
        return md5(serialize([$this->resourceType(), $this->filters(), $this->orders(), $this->chunk()]));
    }
}
