<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Mother\Resource;

use JsonApiPhp\JsonApi\ResourceIdentifier;
use JsonApiPhp\JsonApi\ResourceIdentifierCollection;

final class ResourceIdentifierMother
{
    public static function single(string $type, string $id): ResourceIdentifier
    {
        return new ResourceIdentifier($type, $id);
    }

    public static function collection(string $type, string ...$ids): ResourceIdentifierCollection
    {
        $identifiers = [];

        foreach ($ids as $id) {
            $identifiers[] = self::single($type, $id);
        }

        return new ResourceIdentifierCollection(...$identifiers);
    }
}
