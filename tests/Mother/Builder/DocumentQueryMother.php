<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Mother\Builder;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Query\DocumentsQuery;
use CoderSapient\JsonApi\Query\SingleDocumentQuery;

final class DocumentQueryMother
{
    public static function single(
        ?string $resourceType = null,
        ?string $resourceId = null,
        array $includes = [],
    ): SingleDocumentQuery {
        return (new SingleDocumentQuery(
            $resourceId ?? '1',
            $resourceType ?? 'articles',
        ))->setIncludes(new Includes($includes));
    }

    public static function compound(
        ?string $resourceType = null,
        array $includes = [],
        ?Filters $filters = null,
        ?Orders $orders = null,
        ?int $page = null,
        ?int $perPage = null,
    ): DocumentsQuery {
        return (new DocumentsQuery($resourceType ?? 'articles'))
            ->setFilters($filters ?? new Filters())
            ->setOrders($orders ?? new Orders())
            ->setIncludes(new Includes($includes))
            ->setChunk(new Chunk($page ?? 1, $perPage ?? 15));
    }
}
