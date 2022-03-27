<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Builder;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Member\CountableMember;
use CoderSapient\JsonApi\Query\DocumentsQuery;
use CoderSapient\JsonApi\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\PaginatedCollection;
use JsonApiPhp\JsonApi\ResourceCollection;

class DocumentsBuilder extends Builder
{
    /**
     * Get a document with top-level resources.
     *
     * @param DocumentsQuery $query
     *
     * @return CompoundDocument
     *
     * @throws InvalidArgumentException
     * @throws ResourceResolverNotFoundException
     */
    public function build(DocumentsQuery $query): CompoundDocument
    {
        $resolver = $this->factory->make($query->resourceType());

        $resources = $this->findResources($resolver, $query);

        $includes = $this->buildIncludes($query->includes(), $resources);

        $members = $this->members();

        if ($resolver instanceof PaginationResolver) {
            $response = $resolver->paginate($query);

            $resources = new PaginatedCollection($response->pagination(), $resources);

            $members = array_merge($members, CountableMember::members($response->total(), $query->chunk()));
        }

        $document = new CompoundDocument($resources, new Included(...$includes), ...$members);

        $this->reset();

        return $document;
    }

    /**
     * Get and cache resources by query.
     *
     * @param ResourceResolver $resolver
     * @param DocumentsQuery $query
     *
     * @return ResourceCollection
     */
    protected function findResources(ResourceResolver $resolver, DocumentsQuery $query): ResourceCollection
    {
        $resources = $this->cache->getByQuery($query);

        if ([] === $resources) {
            $resources = $resolver->resolveMany($query);

            $this->cache->setByQuery($query, ...$resources);
        }

        return new ResourceCollection(...$resources);
    }
}
