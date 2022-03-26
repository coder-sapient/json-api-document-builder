<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Resolver;

use CoderSapient\JsonApi\Examples\Assembler\UserResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\UserRepository;
use CoderSapient\JsonApi\Query\DocumentsQuery;
use CoderSapient\JsonApi\Query\SingleDocumentQuery;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use JsonApiPhp\JsonApi\ResourceObject;

final class UserResourceResolver implements ResourceResolver
{
    public function __construct(
        private UserRepository $repository,
        private UserResourceAssembler $assembler,
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
}
