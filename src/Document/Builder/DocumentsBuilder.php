<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Document\Member\CountableMember;
use CoderSapient\JsonApi\Document\Query\DocumentsQuery;
use CoderSapient\JsonApi\Document\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Document\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
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
        $resolver = $this->registry->get($query->resourceType());

        $resources = $this->getResources($resolver, $query);

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
    protected function getResources(ResourceResolver $resolver, DocumentsQuery $query): ResourceCollection
    {
        $resources = $this->cache->getByQuery($query);

        if ([] === $resources) {
            $resources = $resolver->resolveMany($query);

            $this->cache->setByQuery($query, ...$resources);
        }

        return new ResourceCollection(...$resources);
    }
}
