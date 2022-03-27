<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ChunkTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_chunk(): void
    {
        $chunk1 = new Chunk(3, 15);
        $chunk2 = new Chunk(1, 20);

        self::assertSame(3, $chunk1->page());
        self::assertSame(15, $chunk1->perPage());
        self::assertSame(30, $chunk1->offset());

        self::assertSame(1, $chunk2->page());
        self::assertSame(20, $chunk2->perPage());
        self::assertSame(0, $chunk2->offset());
    }

    /** @test */
    public function it_should_throw_an_exception_when_argument_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Chunk(-1, 0);
    }
}
