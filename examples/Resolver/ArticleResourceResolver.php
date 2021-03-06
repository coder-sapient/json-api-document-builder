<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Resolver;

use CoderSapient\JsonApi\Examples\Assembler\ArticleResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\ArticleRepository;
use CoderSapient\JsonApi\Query\DocumentsQuery;
use CoderSapient\JsonApi\Query\SingleDocumentQuery;
use CoderSapient\JsonApi\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Resolver\Response\PaginationResponse;
use JsonApiPhp\JsonApi\Link\FirstLink;
use JsonApiPhp\JsonApi\Link\LastLink;
use JsonApiPhp\JsonApi\Link\NextLink;
use JsonApiPhp\JsonApi\Link\PrevLink;
use JsonApiPhp\JsonApi\Pagination;
use JsonApiPhp\JsonApi\ResourceObject;

final class ArticleResourceResolver implements ResourceResolver, PaginationResolver
{
    public function __construct(
        private ArticleRepository $repository,
        private ArticleResourceAssembler $assembler,
    ) {
    }

    public function resolveOne(SingleDocumentQuery $query): ?ResourceObject
    {
        return $this->assembler->toResource(
            $this->repository->findById($query->resourceId()),
        );
    }

    /**
     * @return ResourceObject[]
     */
    public function resolveMany(DocumentsQuery $query): array
    {
        return $this->assembler->toResources(
            ...$this->repository->match($query),
        );
    }

    /**
     * @return ResourceObject[]
     */
    public function resolveByIds(string ...$resourceIds): array
    {
        return $this->assembler->toResources(
            ...$this->repository->findByIds(...$resourceIds),
        );
    }

    public function paginate(DocumentsQuery $query): PaginationResponse
    {
        return new PaginationResponse(
            1,
            new Pagination(
                new FirstLink('http://localhost/api/v1/articles?page=1&per_page=15'),
                new PrevLink('http://localhost/api/v1/articles?page=1&per_page=15'),
                new NextLink('http://localhost/api/v1/articles?page=1&per_page=15'),
                new LastLink('http://localhost/api/v1/articles?page=1&per_page=15'),
            ),
        );
    }
}
