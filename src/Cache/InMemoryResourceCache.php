<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Utils;
use JsonApiPhp\JsonApi\ResourceObject;

class InMemoryResourceCache implements ResourceCache
{
    private array $cache = [];

    public function getByKey(string $key): ?ResourceObject
    {
        return $this->cache[Utils::typeFromKey($key)]['keys'][$key] ?? null;
    }

    /**
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array
    {
        $resources = [];

        foreach ($keys as $key) {
            $resourceType = Utils::typeFromKey($key);
            if (isset($this->cache[$resourceType]['keys'][$key])) {
                $resources[] = $this->cache[$resourceType]['keys'][$key];
            }
        }

        return $resources;
    }

    /**
     * @return ResourceObject[]
     */
    public function getByCriteria(string $resourceType, Criteria $criteria): array
    {
        return $this->cache[$resourceType]['criteria'][$criteria->key()] ?? [];
    }

    public function setByKeys(ResourceObject ...$resources): void
    {
        foreach ($resources as $resource) {
            $key = $resource->key();
            $this->cache[Utils::typeFromKey($key)]['keys'][$key] = $resource;
        }
    }

    public function setByCriteria(string $resourceType, Criteria $criteria, ResourceObject ...$resources): void
    {
        $this->cache[$resourceType]['criteria'][$criteria->key()] = $resources;
    }

    public function removeByKeys(string ...$keys): void
    {
        foreach ($keys as $key) {
            unset($this->cache[Utils::typeFromKey($key)]['keys'][$key]);
        }
    }

    public function removeByTypes(string ...$resourceTypes): void
    {
        foreach ($resourceTypes as $resourceType) {
            unset($this->cache[$resourceType]);
        }
    }

    public function flush(): void
    {
        $this->cache = [];
    }
}
