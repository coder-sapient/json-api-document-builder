<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Builder;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Query\SingleDocumentQuery;
use CoderSapient\JsonApi\Utils;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

class SingleDocumentBuilder extends Builder
{
    /**
     * Get a document with single top-level resource
     *
     * @param SingleDocumentQuery $query
     *
     * @return CompoundDocument
     *
     * @throws InvalidArgumentException
     * @throws ResourceNotFoundException
     * @throws ResourceResolverNotFoundException
     */
    public function build(SingleDocumentQuery $query): CompoundDocument
    {
        $resource = $this->findResource($query);

        $includes = $this->buildIncludes($query->includes(), new ResourceCollection($resource));

        $document = new CompoundDocument($resource, new Included(...$includes), ...$this->members());

        $this->reset();

        return $document;
    }

    /**
     * Get and cache resource by composite key.
     *
     * @param SingleDocumentQuery $query
     *
     * @return ResourceObject
     *
     * @throws ResourceNotFoundException
     * @throws ResourceResolverNotFoundException
     */
    protected function findResource(SingleDocumentQuery $query): ResourceObject
    {
        $key = Utils::compositeKey($query->resourceType(), $query->resourceId());

        if (null !== $resource = $this->cache->getByKey($key)) {
            return $resource;
        }

        $resolver = $this->factory->make($query->resourceType());

        if (null !== $resource = $resolver->resolveOne($query)) {
            $this->cache->setByKeys($resource);

            return $resource;
        }

        throw new ResourceNotFoundException($key);
    }
}
