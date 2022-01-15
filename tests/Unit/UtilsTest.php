<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit;

use CoderSapient\JsonApi\Utils;
use PHPUnit\Framework\TestCase;

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
}
