<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Criteria\Criteria;
use JsonApiPhp\JsonApi\ResourceObject;

/**
 * @see ResourceObject::key()
 */
interface ResourceCache
{
    public function getByKey(string $key): ?ResourceObject;

    /**
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array;

    /**
     * @return ResourceObject[]
     */
    public function getByCriteria(string $resourceType, Criteria $criteria): array;

    public function setByKeys(ResourceObject ...$resources): void;

    public function setByCriteria(string $resourceType, Criteria $criteria, ResourceObject ...$resources): void;

    public function removeByKeys(string ...$keys): void;

    public function removeByType(string $resourceType): void;

    public function flush(): void;
}
