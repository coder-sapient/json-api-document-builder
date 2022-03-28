<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IncludesTest extends TestCase
{
    /** @test */
    public function it_should_create_a_includes(): void
    {
        $includes = new Includes([
            'articles',
            'articles.comments',
            'articles.comments.users',
        ]);

        self::assertFalse($includes->isEmpty());
        self::assertFalse($includes->hasInclude('users'));
        self::assertTrue($includes->hasInclude('articles'));
        self::assertSame(['comments', 'comments.users'], $includes->getPart('articles')->toArray());
    }

    /** @test */
    public function it_should_throw_an_exception_when_includes_delimiter_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Includes([], '');
    }
}
