<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Document\Member\CountableMember;
use CoderSapient\JsonApi\Resolver\CountableResolver;
use CoderSapient\JsonApi\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\RelatedLink;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\Meta;
use JsonApiPhp\JsonApi\PaginatedCollection;
use JsonApiPhp\JsonApi\ResourceCollection;

class DocumentsBuilder extends Builder
{
    public function build(DocumentsQuery $query, JsonApi|RelatedLink|SelfLink|Meta ...$members): CompoundDocument
    {
        $resolver = $this->registry->get($query->resourceType());
        $criteria = $query->toCriteria();

        $resources = $this->getResources($query->resourceType(), $resolver, $criteria);
        $includes = $this->buildIncludes($query->includes(), $resources);

        if ($resolver instanceof PaginationResolver) {
            $resources = new PaginatedCollection(
                $resolver->pagination($criteria),
                $resources,
            );
        }
        if ($resolver instanceof CountableResolver) {
            $members = array_merge(
                $members,
                CountableMember::members(
                    $resolver->count($criteria),
                    $criteria->chunk()->page(),
                    $criteria->chunk()->perPage(),
                )
            );
        }

        return new CompoundDocument($resources, new Included(...$includes), ...$members);
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
