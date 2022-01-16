<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ChunkTest extends TestCase
{
    /** @test */
    public function it_should_create_a_chunk(): void
    {
        $chunk = new Chunk(3, 15);

        self::assertSame(3, $chunk->page());
        self::assertSame(15, $chunk->perPage());
    }

    /** @test */
    public function it_should_create_a_valid_offset(): void
    {
        $chunk1 = new Chunk(0, 15);
        $chunk2 = new Chunk(1, 15);
        $chunk3 = new Chunk(3, 15);

        self::assertSame(0, $chunk1->offset());
        self::assertSame(0, $chunk2->offset());
        self::assertSame(30, $chunk3->offset());
    }

    /** @test */
    public function it_should_throw_an_exception_when_argument_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Chunk(-1, 0);
    }
}
