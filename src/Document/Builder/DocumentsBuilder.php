<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Document\Member\CountableMember;
use CoderSapient\JsonApi\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\PaginatedCollection;
use JsonApiPhp\JsonApi\ResourceCollection;

class DocumentsBuilder extends Builder
{
    public function build(DocumentsQuery $query): CompoundDocument
    {
        $resolver = $this->registry->get($query->resourceType());
        $criteria = $query->toCriteria();

        $resources = $this->getResources($query->resourceType(), $resolver, $criteria);
        $includes = $this->buildIncludes($query->includes(), $resources);

        $members = $this->members();

        if ($resolver instanceof PaginationResolver) {
            $response = $resolver->paginate($criteria);

            $resources = new PaginatedCollection(
                $response->pagination(),
                $resources,
            );

            $members = array_merge(
                $members,
                CountableMember::members(
                    $response->total(),
                    $criteria->chunk()->page(),
                    $criteria->chunk()->perPage(),
                ),
            );
        }

        $document = new CompoundDocument($resources, new Included(...$includes), ...$members);

        $this->reset();

        return $document;
    }

    protected function getResources(
        string $resourceType,
        ResourceResolver $resolver,
        Criteria $criteria,
    ): ResourceCollection {
        $resources = $this->cache->getByCriteria($resourceType, $criteria);

        if ([] === $resources) {
            $resources = $resolver->matching($criteria);

            $this->cache->setByCriteria($resourceType, $criteria, ...$resources);
        }

        return new ResourceCollection(...$resources);
    }
}
