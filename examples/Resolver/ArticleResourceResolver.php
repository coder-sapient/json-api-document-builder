<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Examples\Assembler\ArticleResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\ArticleRepository;
use CoderSapient\JsonApi\Resolver\CountableResolver;
use CoderSapient\JsonApi\Resolver\PaginationResolver;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use JsonApiPhp\JsonApi\Link\NextLink;
use JsonApiPhp\JsonApi\Pagination;
use JsonApiPhp\JsonApi\ResourceObject;

final class ArticleResourceResolver implements ResourceResolver, CountableResolver, PaginationResolver
{
    public function __construct(
        private ArticleRepository $repository,
        private ArticleResourceAssembler $assembler,
    ) {
    }

    public function getById(string $id): ?ResourceObject
    {
        return $this->assembler->toResource($this->repository->findById($id));
    }

    /**
     * @return ResourceObject[]
     */
    public function getByIds(string ...$ids): array
    {
        return $this->assembler->toResources(...$this->repository->findByIds(...$ids));
    }

    /**
     * @return ResourceObject[]
     */
    public function matching(Criteria $criteria): array
    {
        return $this->assembler->toResources(...$this->repository->match($criteria));
    }

    public function count(Criteria $criteria): int
    {
        return 2;
    }

    public function pagination(Criteria $criteria): Pagination
    {
        return new Pagination(new NextLink('https://example.com/articles?page=2'));
    }
}
