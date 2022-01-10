<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Mother\Builder;

use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\Search;
use CoderSapient\JsonApi\Document\Builder\DocumentsQuery;
use CoderSapient\JsonApi\Document\Builder\SingleDocumentQuery;

final class DocumentQueryMother
{
    public static function single(
        ?string $resourceType = null,
        ?string $resourceId = null,
        array $includes = [],
    ): SingleDocumentQuery {
        return new SingleDocumentQuery(
            $resourceId ?? '1',
            $resourceType ?? 'article',
            new Includes($includes),
        );
    }

    public static function compound(
        ?string $resourceType = null,
        array $includes = [],
        ?Filters $filters = null,
        ?Orders $orders = null,
        ?Search $search = null,
        ?int $page = null,
        ?int $perPage = null,
    ): DocumentsQuery {
        return (new DocumentsQuery($resourceType ?? 'article', new Includes($includes)))
            ->setFilters($filters ?? new Filters())
            ->setOrders($orders ?? new Orders())
            ->setSearch($search)
            ->setPage($page ?? 1)
            ->setPerPage($perPage ?? 15);
    }
}
