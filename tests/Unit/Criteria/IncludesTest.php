<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Includes;
use PHPUnit\Framework\TestCase;

final class IncludesTest extends TestCase
{
    /** @test */
    public function it_should_create_includes(): void
    {
        $includes = new Includes([
            'articles',
            'articles.comments',
            'articles.comments.users',
        ]);

        self::assertFalse($includes->isEmpty());
        self::assertFalse($includes->hasInclude('users'));
        self::assertTrue($includes->hasInclude('articles'));
        self::assertSame(['comments', 'comments.users'], $includes->partOf('articles')->toArray());
    }
}
