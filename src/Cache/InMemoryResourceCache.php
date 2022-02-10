<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Document\Query\JsonApiQuery;
use CoderSapient\JsonApi\Utils;
use JsonApiPhp\JsonApi\ResourceObject;

class InMemoryResourceCache implements ResourceCache
{
    /**
     * @var array
     */
    private array $cacheByKey = [];

    /**
     * @var array
     */
    private array $cacheByQuery = [];

    /**
     * @param string $key
     *
     * @return ResourceObject|null
     */
    public function getByKey(string $key): ?ResourceObject
    {
        return $this->cacheByKey[Utils::typeFromKey($key)][$key] ?? null;
    }

    /**
     * @param string ...$keys
     *
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array
    {
        $resources = [];

        foreach ($keys as $key) {
            $resourceType = Utils::typeFromKey($key);

            if (isset($this->cacheByKey[$resourceType][$key])) {
                $resources[] = $this->cacheByKey[$resourceType][$key];
            }
        }

        return $resources;
    }

    /**
     * @param JsonApiQuery $query
     *
     * @return ResourceObject[]
     */
    public function getByQuery(JsonApiQuery $query): array
    {
        return $this->cacheByQuery[$query->resourceType()][$query->key()] ?? [];
    }

    /**
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByKeys(ResourceObject ...$resources): void
    {
        foreach ($resources as $resource) {
            $key = $resource->key();
            $this->cacheByKey[Utils::typeFromKey($key)][$key] = $resource;
        }
    }

    /**
     * @param JsonApiQuery $query
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByQuery(JsonApiQuery $query, ResourceObject ...$resources): void
    {
        $this->cacheByQuery[$query->resourceType()][$query->key()] = $resources;
    }

    /**
     * @param string ...$keys
     *
     * @return void
     */
    public function removeByKeys(string ...$keys): void
    {
        foreach ($keys as $key) {
            unset($this->cacheByKey[Utils::typeFromKey($key)][$key]);
        }
    }

    /**
     * @param string ...$resourceTypes
     *
     * @return void
     */
    public function removeByTypes(string ...$resourceTypes): void
    {
        foreach ($resourceTypes as $resourceType) {
            unset($this->cacheByKey[$resourceType], $this->cacheByQuery[$resourceType]);
        }
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->cacheByKey = $this->cacheByQuery = [];
    }
}
