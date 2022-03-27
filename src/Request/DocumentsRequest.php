<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Request;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Filter;
use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Order;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\OrderType;
use CoderSapient\JsonApi\Exception\BadRequestException;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Query\DocumentsQuery;
use CoderSapient\JsonApi\Utils;

trait DocumentsRequest
{
    use JsonApiRequest;

    /** @var int  */
    protected int $defaultPage = 1;

    /** @var int  */
    protected int $defaultPerPage = 15;

    /** @var string */
    protected string $queryPage = 'page';

    /** @var string */
    protected string $queryPerPage = 'per_page';

    /** @var string */
    protected string $queryFilter = 'filter';

    /** @var string */
    protected string $querySort = 'sort';

    /** @var string */
    protected string $filterDelimiter = ',';

    /** @var string */
    protected string $sortDelimiter = ',';

    /** @var string */
    protected string $sortPrefix = '-';

    /**
     * @return DocumentsQuery
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function toQuery(): DocumentsQuery
    {
        $this->ensureQueryParamsIsValid();

        return (new DocumentsQuery($this->resourceType()))
            ->setChunk($this->chunk())
            ->setFilters($this->filters())
            ->setOrders($this->orders())
            ->setIncludes($this->includes());
    }

    /**
     * @return array
     */
    public function acceptableQueryParams(): array
    {
        return [
            $this->queryPage,
            $this->queryPerPage,
            $this->queryFilter,
            $this->querySort,
            $this->queryInclude,
        ];
    }

    /**
     * Example: [
     *   'created_at' => ['eq', 'gt', 'lt'],
     *   'title' => ['eq', 'like'],
     * ]
     *
     * @return array
     */
    public function acceptableFilters(): array
    {
        return [];
    }

    /**
     * Example: ['created_at'].
     *
     * @return array
     */
    public function acceptableSorting(): array
    {
        return [];
    }

    /**
     * @return Chunk
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function chunk(): Chunk
    {
        $page = $this->queryParam($this->queryPage, $this->defaultPage);
        $perPage = $this->queryParam($this->queryPerPage, $this->defaultPerPage);

        $this->ensureQueryParamIsPositiveInt($this->queryPage, $page);
        $this->ensureQueryParamIsPositiveInt($this->queryPerPage, $perPage);

        return new Chunk((int) $page, (int) $perPage);
    }

    /**
     * @return Filters
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function filters(): Filters
    {
        $filter = $this->queryParam($this->queryFilter, []);

        $this->ensureFilterIsValid($filter);

        $filters = [];

        foreach ($this->normalizeFilter($filter) as $field => $condition) {
            foreach ($condition as $operator => $value) {
                $filters[] = Filter::fromValues($field, $operator, $value);
            }
        }

        return new Filters(...$filters);
    }

    /**
     * @return Orders
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function orders(): Orders
    {
        $sort = $this->queryParam($this->querySort, '');

        $this->ensureSortIsValid($sort);

        $orders = [];

        foreach (Utils::explodeIfNotEmpty($sort, $this->sortDelimiter) as $field) {
            $fieldWithoutPrefix = Utils::subStrFirst($field, $this->sortPrefix);
            $type = $fieldWithoutPrefix === $field ? OrderType::ASC : OrderType::DESC;

            $orders[] = Order::fromValues($fieldWithoutPrefix, $type);
        }

        return new Orders(...$orders);
    }

    /**
     * @param mixed $sort
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureSortIsValid(mixed $sort): void
    {
        $this->ensureQueryParamIsString($this->querySort, $sort);

        $sort = array_map(
            fn (string $value) => Utils::subStrFirst($value, $this->sortPrefix),
            Utils::explodeIfNotEmpty($sort, $this->sortDelimiter),
        );

        $this->ensureQueryParamValueIsAcceptable($this->querySort, $sort, $this->acceptableSorting());
    }

    /**
     * @param mixed $filter
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureFilterIsValid(mixed $filter): void
    {
        $this->ensureQueryParamIsArray($this->queryFilter, $filter);

        $this->ensureFilterHasAValidStructure($filter);

        $this->ensureFilterIsAcceptable($filter);
    }

    /**
     * @param array $filter
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureFilterHasAValidStructure(array $filter): void
    {
        foreach ($filter as $condition) {
            foreach ((array) $condition as $value) {
                if (is_array($value)) {
                    $this->throwBadRequestException(
                        sprintf(
                            "%s should have the following structure [%s[field][operator]=value]",
                            $this->queryFilter,
                            $this->queryFilter,
                        ),
                        $this->queryFilter,
                    );
                }
            }
        }
    }

    /**
     * @param array $filter
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureFilterIsAcceptable(array $filter): void
    {
        foreach ($this->normalizeFilter($filter) as $field => $condition) {
            foreach ($condition as $operator => $value) {
                if (
                    ! isset($this->acceptableFilters()[$field])
                    || ! in_array($operator, $this->acceptableFilters()[$field], true)
                ) {
                    $this->throwBadRequestException(
                        sprintf(
                            "Not acceptable %s operator [%s] for field [%s]",
                            $this->queryFilter,
                            $operator,
                            $field,
                        ),
                        $this->queryFilter,
                    );
                }
            }
        }
    }

    /**
     * @param array $filter
     *
     * @return array
     */
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
