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

final class UtilsTest extends TestCase
{
    /** @test */
    public function it_should_sub_str_first_symbol(): void
    {
        self::assertSame('value', Utils::subStrFirst('-value', '-'));
        self::assertSame('-value', Utils::subStrFirst('-value', '+'));
    }

    /** @test */
    public function it_should_explode_string_if_string_is_not_empty(): void
    {
        self::assertSame([], Utils::explodeIfNotEmpty('', ','));
        self::assertSame(['foo', 'bar'], Utils::explodeIfNotEmpty('foo,bar', ','));
    }

    /** @test */
    public function it_should_explode_string_if_string_contains_delimiter(): void
    {
        self::assertSame('', Utils::explodeIfContains('', ','));
        self::assertSame(['foo', 'bar'], Utils::explodeIfContains('foo,bar', ','));
    }

    /** @test */
    public function it_should_get_resource_type_from_key(): void
    {
        self::assertSame('articles', Utils::getType(Utils::compositeKey('articles', '1')));
    }

    /** @test */
    public function it_should_create_a_composite_key(): void
    {
        self::assertSame('articles:1', Utils::compositeKey('articles', '1'));
    }

    /** @test */
    public function it_should_split_key_into_resource_type_and_resource_id(): void
    {
        $key = Utils::compositeKey('articles', '1');

        [$resourceType, $resourceId] = Utils::splitKey($key);

        self::assertSame('1', $resourceId);
        self::assertSame('articles', $resourceType);
    }
}
