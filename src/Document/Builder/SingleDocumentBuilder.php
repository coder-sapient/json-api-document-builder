<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\Meta;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

class SingleDocumentBuilder extends Builder
{
    public function build(SingleDocumentQuery $query, JsonApi|SelfLink|Meta ...$members): CompoundDocument
    {
        $resource = $this->getResource(
            $query->resourceType(),
            $query->resourceId(),
        );
        $includes = $this->buildIncludes(
            $query->includes(),
            new ResourceCollection($resource),
        );

        return new CompoundDocument($resource, new Included(...$includes), ...$members);
    }

    protected function getResource(string $resourceType, string $resourceId): ResourceObject
    {
        $key = $this->compositeKey($resourceId, $resourceType);

        if (null !== $resource = $this->cache->getOne($key)) {
            return $resource;
        }

        $resolver = $this->registry->get($resourceType);

        if (null === $resource = $resolver->getById($resourceId)) {
            throw new ResourceNotFoundException($key);
        }

        return $resource;
    }
}
