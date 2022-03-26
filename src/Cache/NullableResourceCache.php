<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Query\JsonApiQuery;
use JsonApiPhp\JsonApi\ResourceObject;

class NullableResourceCache implements ResourceCache
{
    /**
     * @param string $key
     *
     * @return ResourceObject|null
     */
    public function getByKey(string $key): ?ResourceObject
    {
        return null;
    }

    /**
     * @param string ...$keys
     *
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array
    {
        return [];
    }

    /**
     * @param JsonApiQuery $query
     *
     * @return ResourceObject[]
     */
    public function getByQuery(JsonApiQuery $query): array
    {
        return [];
    }

    /**
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByKeys(ResourceObject ...$resources): void
    {
    }

    /**
     * @param JsonApiQuery $query
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByQuery(JsonApiQuery $query, ResourceObject ...$resources): void
    {
    }

    /**
     * @param string ...$keys
     *
     * @return void
     */
    public function removeByKeys(string ...$keys): void
    {
    }

    /**
     * @param string ...$resourceTypes
     *
     * @return void
     */
    public function removeByTypes(string ...$resourceTypes): void
    {
    }

    /**
     * @return void
     */
    public function flush(): void
    {
    }
}
