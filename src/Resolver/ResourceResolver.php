<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use GuzzleHttp\Promise\PromiseInterface;
use JsonApiPhp\JsonApi\ResourceObject;

interface ResourceResolver
{
    public function getById(string $resourceId): ?ResourceObject;

    /**
     * @return ResourceObject[]|PromiseInterface
     */
    public function getByIds(string ...$resourceIds): array|PromiseInterface;

    /**
     * @return ResourceObject[]
     */
    public function matching(Criteria $criteria): array;
}
