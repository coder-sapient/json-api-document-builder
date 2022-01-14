<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Examples\Assembler\ArticleResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\ArticleRepository;
use CoderSapient\JsonApi\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Resolver\Response\PaginationResponse;
use JsonApiPhp\JsonApi\Link\FirstLink;
use JsonApiPhp\JsonApi\Pagination;
use JsonApiPhp\JsonApi\ResourceObject;

final class ArticleResourceResolver implements ResourceResolver, PaginationResolver
{
    public function __construct(
        private ArticleRepository $repository,
        private ArticleResourceAssembler $assembler,
    ) {
    }

    public function getById(string $resourceId): ?ResourceObject
    {
        return $this->assembler->toResource(
            $this->repository->findById($resourceId),
        );
    }

    /**
     * @return ResourceObject[]
     */
    public function getByIds(string ...$resourceIds): array
    {
        return $this->assembler->toResources(
            ...$this->repository->findByIds(...$resourceIds),
        );
    }

    /**
     * @return ResourceObject[]
     */
    public function matching(Criteria $criteria): array
    {
        return $this->assembler->toResources(
            ...$this->repository->match($criteria),
        );
    }

    public function resolve(Criteria $criteria): PaginationResponse
    {
        return new PaginationResponse(
            1,
            new Pagination(
                new FirstLink('https://example.com/articles?page=1&per_page=15'),
            ),
        );
    }
}
