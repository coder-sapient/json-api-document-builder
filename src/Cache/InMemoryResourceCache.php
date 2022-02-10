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
    private array $keys = [];

    /**
     * @var array
     */
    private array $queries = [];

    /**
     * @param string $key
     *
     * @return ResourceObject|null
     */
    public function getByKey(string $key): ?ResourceObject
    {
        return $this->keys[Utils::typeOf($key)][$key] ?? null;
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
            $resourceType = Utils::typeOf($key);

            if (isset($this->keys[$resourceType][$key])) {
                $resources[] = $this->keys[$resourceType][$key];
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
        return $this->queries[$query->resourceType()][$query->hash()] ?? [];
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
            $this->keys[Utils::typeOf($key)][$key] = $resource;
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
        $this->queries[$query->resourceType()][$query->hash()] = $resources;
    }

    /**
     * @param string ...$keys
     *
     * @return void
     */
    public function removeByKeys(string ...$keys): void
    {
        foreach ($keys as $key) {
            unset($this->keys[Utils::typeOf($key)][$key]);
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
            unset($this->keys[$resourceType], $this->queries[$resourceType]);
        }
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->keys = $this->queries = [];
    }
}
