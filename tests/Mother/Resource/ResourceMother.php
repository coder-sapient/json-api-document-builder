<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Mother\Resource;

use JsonApiPhp\JsonApi\ResourceObject;
use JsonApiPhp\JsonApi\ToMany;
use JsonApiPhp\JsonApi\ToOne;

final class ResourceMother
{
    public static function create(
        ?string $id = null,
        ?string $type = null,
        array $relationships = [],
    ): ResourceObject {
        return new ResourceObject(
            $type ?? 'articles',
            $id ?? '1',
            ...self::relationships($relationships),
        );
    }

    public static function relationships(array $relationships): array
    {
        $members = [];

        foreach ($relationships as [$relationName, $resourceType, $resourceIds]) {
            if (is_array($resourceIds)) {
                $members[] = new ToMany(
                    $relationName,
                    ResourceIdentifierMother::collection($resourceType, ...$resourceIds),
                );
            } else {
                $members[] = new ToOne(
                    $relationName,
                    ResourceIdentifierMother::single($resourceType, $resourceIds),
                );
            }
        }

        return $members;
    }
}
