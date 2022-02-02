<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Criteria\Criteria;
use JsonApiPhp\JsonApi\ResourceObject;

class InMemoryResourceCache implements ResourceCache
{
    private array $cache = [];

    public function getByKey(string $key): ?ResourceObject
    {
        return $this->cache['keys'][$key] ?? null;
    }

    /**
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array
    {
        $resources = [];

        foreach ($keys as $key) {
            if (isset($this->cache['keys'][$key])) {
                $resources[] = $this->cache['keys'][$key];
            }
        }

        return $resources;
    }

    /**
     * @return ResourceObject[]
     */
    public function getByCriteria(string $resourceType, Criteria $criteria): array
    {
        return $this->cache['criteria'][$resourceType][$criteria->key()] ?? [];
    }

    public function setByKeys(ResourceObject ...$resources): void
    {
        foreach ($resources as $resource) {
            $this->cache['keys'][$resource->key()] = $resource;
        }
    }

    public function setByCriteria(string $resourceType, Criteria $criteria, ResourceObject ...$resources): void
    {
        $this->cache['criteria'][$resourceType][$criteria->key()] = $resources;
    }

    public function removeByKeys(string ...$keys): void
    {
        foreach ($keys as $key) {
            unset($this->cache['keys'][$key]);
        }
    }

    public function removeByType(string $resourceType): void
    {
        foreach ($this->cache['keys'] as $key => $resource) {
            if (str_starts_with($key, $resourceType)) {
                unset($this->cache['keys'][$key]);
            }
        }

        unset($this->cache['criteria'][$resourceType]);
    }

    public function flush(): void
    {
        $this->cache = [];
    }
}
