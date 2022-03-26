<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Resolver;

use CoderSapient\JsonApi\Query\DocumentsQuery;
use CoderSapient\JsonApi\Query\SingleDocumentQuery;
use GuzzleHttp\Promise\PromiseInterface;
use JsonApiPhp\JsonApi\ResourceObject;

interface ResourceResolver
{
    /**
     * @param DocumentsQuery $query
     *
     * @return ResourceObject[]
     */
    public function resolveMany(DocumentsQuery $query): array;

    /**
     * @param SingleDocumentQuery $query
     *
     * @return ResourceObject|null
     */
    public function resolveOne(SingleDocumentQuery $query): ?ResourceObject;

    /**
     * @param string ...$resourceIds
     *
     * @return ResourceObject[]|PromiseInterface
     */
    public function resolveByIds(string ...$resourceIds): array|PromiseInterface;
}
