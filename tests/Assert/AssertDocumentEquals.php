<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Assert;

trait AssertDocumentEquals
{
    public static function assertEncodesTo(string $expected, $document): void
    {
        self::assertEquals(
            json_decode($expected),
            json_decode(json_encode($document, \JSON_UNESCAPED_SLASHES)),
        );
    }
}
