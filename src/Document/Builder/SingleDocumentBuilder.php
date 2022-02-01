<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

class SingleDocumentBuilder extends Builder
{
    public function build(SingleDocumentQuery $query): CompoundDocument
    {
        $resource = $this->getResource(
            $query->resourceId(),
            $query->resourceType(),
        );
        $includes = $this->buildIncludes(
            $query->includes(),
            new ResourceCollection($resource),
        );

        $document = new CompoundDocument(
            $resource, new Included(...$includes), ...$this->members(),
        );

        $this->reset();

        return $document;
    }

    protected function getResource(string $resourceId, string $resourceType): ResourceObject
    {
        $key = $this->compositeKey($resourceId, $resourceType);

        if (null !== $resource = $this->cache->getOne($key)) {
            return $resource;
        }

        $resolver = $this->registry->get($resourceType);

        if (null === $resource = $resolver->resolveById($resourceId)) {
            throw new ResourceNotFoundException($key);
        }

        $this->cache->set($resource);

        return $resource;
    }
}
