<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Http\Request;

use CoderSapient\JsonApi\Criteria\Filter;
use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Order;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\OrderType;
use CoderSapient\JsonApi\Criteria\Search;
use CoderSapient\JsonApi\Criteria\SearchType;
use CoderSapient\JsonApi\Document\Builder\DocumentsQuery;
use CoderSapient\JsonApi\Utils;

trait DocumentsRequest
{
    use JsonApiRequest;

    protected string $queryPage = 'page';
    protected string $queryPerPage = 'per_page';
    protected string $queryFilter = 'filter';
    protected string $querySearch = 'search';
    protected string $querySort = 'sort';

    protected string $filterDelimiter = ',';
    protected string $sortDelimiter = ',';

    protected string $sortPrefix = '-';
    protected string $searchPrefix = '^';

    public function toQuery(): DocumentsQuery
    {
        $this->ensureQueryParametersIsValid();

        return (new DocumentsQuery($this->resourceType(), $this->includes()))
            ->setFilters($this->filters())
            ->setOrders($this->orders())
            ->setSearch($this->search())
            ->setPerPage($this->perPage())
            ->setPage($this->page());
    }

    protected function supportedQueryParams(): array
    {
        return [
            $this->queryPage, $this->queryPerPage,
            $this->queryFilter, $this->querySearch,
            $this->querySort, $this->queryInclude,
        ];
    }

    /**
     * [
     *   'foo' => ['eq', 'gt', 'lt'],
     *   'bar' => ['like', 'lt'],
     * ].
     */
    protected function supportedFilters(): array
    {
        return [];
    }

    /**
     * ['foo', 'bar'].
     */
    protected function supportedSorting(): array
    {
        return [];
    }

    protected function page(): int
    {
        $page = $this->queryParam($this->queryPage, DocumentsQuery::DEFAULT_PAGE);

        $this->ensurePageIsValid($page);

        return (int) $page;
    }

    protected function perPage(): int
    {
        $perPage = $this->queryParam($this->queryPerPage, DocumentsQuery::DEFAULT_PAGE);

        $this->ensurePerPageIsValid($perPage);

        return (int) $perPage;
    }

    protected function search(): ?Search
    {
        $search = $this->queryParam($this->querySearch, '');

        $this->ensureSearchIsValid($search);

        if ('' === $search) {
            return null;
        }

        $query = Utils::subStrFirst($search, $this->searchPrefix);
        $type = $search === $query ? SearchType::BY_PHRASE : SearchType::BY_PREFIX;

        return Search::fromValues($query, $type);
    }

    protected function filters(): Filters
    {
        $filter = $this->queryParam($this->queryFilter, []);

        $this->ensureFilterIsValid($filter);

        $collect = new Filters();

        foreach ($this->normalizeFilter($filter) as $field => $filter) {
            foreach ($filter as $operator => $value) {
                $collect->add(Filter::fromValues($field, $operator, $value));
            }
        }

        return $collect;
    }

    protected function orders(): Orders
    {
        $sort = $this->queryParam($this->querySort, '');

        $this->ensureSortIsValid($sort);

        $collect = new Orders();

        foreach (Utils::explodeIfNotEmpty($sort, $this->sortDelimiter) as $field) {
            $by = Utils::subStrFirst($field, $this->sortPrefix);
            $type = $by === $field ? OrderType::ASC : OrderType::DESC;

            $collect->add(Order::fromValues($by, $type));
        }

        return $collect;
    }

    protected function ensurePageIsValid(mixed $page): void
    {
        if (! is_numeric($page) || (int) $page < 0) {
            $this->throwBadRequestException('Page must be a non-negative integer', $this->queryPage);
        }
    }

    protected function ensurePerPageIsValid(mixed $perPage): void
    {
        if (! is_numeric($perPage) || (int) $perPage < 1) {
            $this->throwBadRequestException('PerPage must be a positive integer', $this->queryPerPage);
        }
    }

    protected function ensureSearchIsValid(mixed $query): void
    {
        $this->ensureQueryParamIsString($this->querySearch, $query);
    }

    protected function ensureFilterIsValid(mixed $filter): void
    {
        $this->ensureQueryParamIsArray($this->queryFilter, $filter);
        $this->ensureFilterIsSupported($filter);
    }

    protected function ensureSortIsValid(mixed $sort): void
    {
        $this->ensureQueryParamIsString($this->querySort, $sort);

        $sort = array_map(
            fn (string $value) => Utils::subStrFirst($value, $this->sortPrefix),
            Utils::explodeIfNotEmpty($sort, $this->sortDelimiter),
        );

        $this->ensureQueryParamIsSupported($this->querySort, $sort, $this->supportedSorting());
    }

    protected function ensureFilterIsSupported(array $filter): void
    {
        foreach ($this->normalizeFilter($filter) as $field => $data) {
            foreach ($data as $operator => $value) {
                if (
                    ! isset($this->supportedFilters()[$field])
                    || ! in_array($operator, $this->supportedFilters()[$field], true)
                ) {
                    $this->throwBadRequestException(
                        "Not allowed filter [{$operator}] for field [{$field}]",
                        $this->queryFilter,
                    );
                }
            }
        }
    }

    protected function normalizeFilter(array $filter): array
    {
        $result = [];

        foreach ($filter as $field => $condition) {
            if (is_array($condition)) {
                foreach ($condition as $operator => $value) {
                    $result[$field][$operator] = Utils::explodeIfContains(
                        $value,
                        $this->filterDelimiter,
                    );
                }
            } else {
                $result[$field][FilterOperator::EQUAL] = Utils::explodeIfContains(
                    $condition,
                    $this->filterDelimiter,
                );
            }
        }

        return $result;
    }
}
