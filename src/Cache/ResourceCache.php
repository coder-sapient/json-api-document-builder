<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Cache;

use CoderSapient\JsonApi\Document\Query\JsonApiQuery;
use JsonApiPhp\JsonApi\ResourceObject;

/**
 * @see ResourceObject::key()
 */
interface ResourceCache
{
    /**
     * @param string $key
     *
     * @return ResourceObject|null
     */
    public function getByKey(string $key): ?ResourceObject;

    /**
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array;

    /**
     * @param JsonApiQuery $query
     *
     * @return ResourceObject[]
     */
    public function getByQuery(JsonApiQuery $query): array;

    /**
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByKeys(ResourceObject ...$resources): void;

    /**
     * @param JsonApiQuery $query
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByQuery(JsonApiQuery $query, ResourceObject ...$resources): void;

    /**
     * @param string ...$keys
     *
     * @return void
     */
    public function removeByKeys(string ...$keys): void;

    /**
     * @param string ...$resourceTypes
     *
     * @return void
     */
    public function removeByTypes(string ...$resourceTypes): void;

    /**
     * @return void
     */
    public function flush(): void;
}
