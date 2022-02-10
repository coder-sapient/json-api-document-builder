<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Document\Query\SingleDocumentQuery;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

use function JsonApiPhp\JsonApi\compositeKey;

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
        $resource = $this->getResource($query);

        $includes = $this->buildIncludes($query->includes(), new ResourceCollection($resource));

        $document = new CompoundDocument(
            $resource, new Included(...$includes), ...$this->members(),
        );

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
    protected function getResource(SingleDocumentQuery $query): ResourceObject
    {
        $key = compositeKey($query->resourceType(), $query->resourceId());

        if (null !== $resource = $this->cache->getByKey($key)) {
            return $resource;
        }

        $resolver = $this->registry->get($query->resourceType());

        if (null !== $resource = $resolver->resolveOne($query)) {
            $this->cache->setByKeys($resource);

            return $resource;
        }

        throw new ResourceNotFoundException($key);
    }
}
