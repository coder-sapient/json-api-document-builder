<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Criteria\Criteria;
use JsonApiPhp\JsonApi\ResourceObject;

interface ResourceCache
{
    public function getOne(string $key): ?ResourceObject;

    /**
     * @return ResourceObject[]
     */
    public function getMany(string ...$keys): array;

    /**
     * @return ResourceObject[]
     */
    public function getByCriteria(string $resourceType, Criteria $criteria): array;

    public function set(ResourceObject ...$resources): void;

    public function setByCriteria(string $resourceType, Criteria $criteria, ResourceObject ...$resources): void;

    public function remove(string ...$keys): void;

    public function removeByCriteria(string $resourceType, Criteria $criteria): void;
}
