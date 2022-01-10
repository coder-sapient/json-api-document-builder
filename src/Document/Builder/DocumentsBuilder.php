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
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\Meta;
use JsonApiPhp\JsonApi\PaginatedCollection;
use JsonApiPhp\JsonApi\Pagination;
use JsonApiPhp\JsonApi\ResourceCollection;

class DocumentsBuilder extends Builder
{
    public function build(DocumentsQuery $query, JsonApi|SelfLink|Meta ...$members): CompoundDocument
    {
        $resolver = $this->registry->get($query->resourceType());
        $criteria = $query->toCriteria();

        $resources = $this->getResources($query->resourceType(), $resolver, $criteria);

        $includes = $this->buildIncludes($query->includes(), $resources);
        $pagination = $this->buildPagination($resolver, $criteria);
        $members = array_merge($this->buildMetaMembers($resolver, $criteria), $members);

        if (null !== $pagination) {
            $resources = new PaginatedCollection($pagination, $resources);
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

    protected function buildPagination(ResourceResolver $resolver, Criteria $criteria): ?Pagination
    {
        return $resolver instanceof PaginationResolver ? $resolver->pagination($criteria) : null;
    }

    /**
     * @return Meta[]
     */
    protected function buildMetaMembers(ResourceResolver $resolver, Criteria $criteria): array
    {
        return $resolver instanceof CountableResolver ? CountableMember::members(
            $resolver->count($criteria),
            $criteria->chunk()->page(),
            $criteria->chunk()->perPage(),
        ) : [];
    }
}
