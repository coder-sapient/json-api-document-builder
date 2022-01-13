<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Resolver;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Examples\Assembler\ArticleResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\ArticleRepository;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use JsonApiPhp\JsonApi\ResourceObject;

final class ArticleResourceResolver implements ResourceResolver
{
    public function __construct(
        private ArticleRepository $repository,
        private ArticleResourceAssembler $assembler,
    ) {
    }

    public function getById(string $resourceId): ?ResourceObject
    {
        return $this->assembler->toResource($this->repository->findById($resourceId));
    }

    /**
     * @return ResourceObject[]
     */
    public function getByIds(string ...$resourceIds): array
    {
        return $this->assembler->toResources(...$this->repository->findByIds(...$resourceIds));
    }

    /**
     * @return ResourceObject[]
     */
    public function matching(Criteria $criteria): array
    {
        return $this->assembler->toResources(...$this->repository->match($criteria));
    }
}
