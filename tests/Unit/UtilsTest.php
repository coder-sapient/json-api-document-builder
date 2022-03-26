<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit;

use CoderSapient\JsonApi\Utils;
use PHPUnit\Framework\TestCase;

use function JsonApiPhp\JsonApi\compositeKey;

final class UtilsTest extends TestCase
{
    /** @test */
    public function sub_str_first(): void
    {
        self::assertSame('value', Utils::subStrFirst('-value', '-'));
        self::assertSame('-value', Utils::subStrFirst('-value', '+'));
    }

    /** @test */
    public function explode_if_not_empty(): void
    {
        self::assertSame([], Utils::explodeIfNotEmpty('', ','));
        self::assertSame(['foo', 'bar'], Utils::explodeIfNotEmpty('foo,bar', ','));
    }

    /** @test */
    public function explode_if_contains(): void
    {
        self::assertSame('', Utils::explodeIfContains('', ','));
        self::assertSame(['foo', 'bar'], Utils::explodeIfContains('foo,bar', ','));
    }

    /** @test */
    public function get_resource_type_from_key(): void
    {
        self::assertSame('articles', Utils::getType(compositeKey('articles', '1')));
    }

    /** @test */
    public function slip_key_into_resource_type_and_id(): void
    {
        $key = compositeKey('articles', '1');

        [$resourceType, $resourceId] = Utils::splitKey($key);

        self::assertSame('1', $resourceId);
        self::assertSame('articles', $resourceType);
    }
}
