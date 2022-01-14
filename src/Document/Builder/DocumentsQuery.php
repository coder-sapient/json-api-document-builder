<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Criteria\Orders;

class DocumentsQuery
{
    public const DEFAULT_PER_PAGE = 15;
    public const DEFAULT_PAGE = 1;

    private int $perPage = self::DEFAULT_PER_PAGE;
    private int $page = self::DEFAULT_PAGE;

    private ?Filters $filters = null;
    private ?Orders $orders = null;
    private ?Includes $includes = null;

    public function __construct(private string $resourceType)
    {
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

    public function includes(): Includes
    {
        return $this->includes ?? new Includes();
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

    public function setIncludes(Includes $includes): self
    {
        $this->includes = $includes;

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
        );
    }
}
