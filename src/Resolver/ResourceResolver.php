<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use JsonApiPhp\JsonApi\ResourceObject;

interface ResourceResolver
{
    public function getById(string $id): ?ResourceObject;

    /**
     * @return ResourceObject[]
     */
    public function getByIds(string ...$ids): array;

    /**
     * @return ResourceObject[]
     */
    public function matching(Criteria $criteria): array;
}
