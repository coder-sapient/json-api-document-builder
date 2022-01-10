<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Mother\Resource;

use JsonApiPhp\JsonApi\ResourceObject;
use JsonApiPhp\JsonApi\ToMany;
use JsonApiPhp\JsonApi\ToOne;

final class ResourceMother
{
    public static function create(?string $id = null, ?string $type = null, array $relations = []): ResourceObject
    {
        return new ResourceObject(
            $type ?? 'articles',
            $id ?? '1',
            ...self::relations($relations),
        );
    }

    public static function relations(array $relations): array
    {
        $members = [];

        foreach ($relations as [$relationType, $relationName, $resourceType, $resourceIds]) {
            if ('to_one' === $relationType) {
                $members[] = new ToOne(
                    $relationName,
                    ResourceIdentifierMother::single($resourceType, $resourceIds),
                );
            } elseif ('to_many' === $relationType) {
                $members[] = new ToMany(
                    $relationName,
                    ResourceIdentifierMother::collection($resourceType, ...$resourceIds),
                );
            }
        }

        return $members;
    }
}
