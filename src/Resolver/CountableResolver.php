<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;

interface CountableResolver
{
    public function count(Criteria $criteria): int;
}
