<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Mother\Criteria;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\Search;

final class CriteriaMother
{
    public static function create(
        ?Filters $filters = null,
        ?Orders $orders = null,
        ?Chunk $chunk = null,
    ): Criteria {
        return new Criteria(
            $filters ?? new Filters(),
            $orders ?? new Orders(),
            $chunk ?? new Chunk(1, 1),
        );
    }
}
