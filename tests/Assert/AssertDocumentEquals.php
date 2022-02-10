<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
