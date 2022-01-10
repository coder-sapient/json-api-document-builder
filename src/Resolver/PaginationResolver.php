<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use JsonApiPhp\JsonApi\Pagination;

interface PaginationResolver
{
    public function pagination(Criteria $criteria): Pagination;
}
