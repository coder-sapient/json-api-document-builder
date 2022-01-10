<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\Search;

class DocumentsQuery
{
    public const DEFAULT_PER_PAGE = 15;
    public const DEFAULT_PAGE = 1;

    private int $perPage = self::DEFAULT_PER_PAGE;
    private int $page = self::DEFAULT_PAGE;

    private ?Filters $filters = null;
    private ?Orders $orders = null;
    private ?Search $search = null;

    public function __construct(
        private string $resourceType,
        private Includes $includes,
    ) {
    }

    public function resourceType(): string
    {
        return $this->resourceType;
    }

    public function filters(): Filters
    {
        return $this->filters ?? new Filters();
    }

    public function orders(): Orders
    {
        return $this->orders ?? new Orders();
    }

    public function search(): ?Search
    {
        return $this->search;
    }

    public function includes(): Includes
    {
        return $this->includes;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function setFilters(Filters $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function setOrders(Orders $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function setSearch(?Search $search = null): self
    {
        $this->search = $search;

        return $this;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function toCriteria(): Criteria
    {
        return new Criteria(
            $this->filters(),
            $this->orders(),
            new Chunk($this->page(), $this->perPage()),
            $this->search(),
        );
    }
}
