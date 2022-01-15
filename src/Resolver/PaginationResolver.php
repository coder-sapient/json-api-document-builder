<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Resolver\Response\PaginationResponse;

interface PaginationResolver
{
    public function paginate(Criteria $criteria): PaginationResponse;
}
