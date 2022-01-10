<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Assert;

use JsonApiPhp\JsonApi\CompoundDocument;

trait AssertDocumentEquals
{
    public static function assertEncodesTo(string $expected, CompoundDocument $document): void
    {
        self::assertEquals(
            json_decode($expected),
            json_decode(json_encode($document, \JSON_UNESCAPED_SLASHES)),
        );
    }
}
