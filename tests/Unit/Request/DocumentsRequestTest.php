<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Request;

use CoderSapient\JsonApi\Criteria\Filter;
use CoderSapient\JsonApi\Criteria\Order;
use CoderSapient\JsonApi\Exception\BadRequestException;
use CoderSapient\JsonApi\Tests\Fake\Request\FakeDocumentsRequest;
use PHPUnit\Framework\TestCase;

final class DocumentsRequestTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_document_query(): void
    {
        $request = new FakeDocumentsRequest(
            [
                'filter' => [
                    'title' => 'Hello World',
                    'published_at' => [
                        'gt' => '2021-10-20',
                        'lte' => '2021-11-20',
                    ],
                ],
                'sort' => '-title,published_at',
                'include' => 'author,comments',
                'page' => '2',
                'per_page' => '20',
            ],
        );

        $query = $request->toQuery();

        self::assertSame('articles', $query->resourceType());
        self::assertSame(2, $query->page());
        self::assertSame(20, $query->perPage());

        self::assertTrue($query->includes()->hasInclude('author'));
        self::assertTrue($query->includes()->hasInclude('comments'));

        /**
         * @var int $index
         * @var Order $order
         */
        foreach ($query->orders() as $index => $order) {
            switch ($index) {
                case 0:
                    self::assertSame('title', $order->by());
                    self::assertFalse($order->type()->isAsc());
                    break;

                case 1:
                    self::assertSame('published_at', $order->by());
                    self::assertTrue($order->type()->isAsc());
                    break;
            }
        }

        /**
         * @var int $index
         * @var Filter $filter
         */
        foreach ($query->filters() as $index => $filter) {
            switch ($index) {
                case 0:
                    self::assertSame('title', $filter->field());
                    self::assertTrue($filter->operator()->isEqual('eq'));
                    self::assertSame('Hello World', $filter->value());
                    break;

                case 1:
                    self::assertSame('published_at', $filter->field());
                    self::assertTrue($filter->operator()->isEqual('gt'));
                    self::assertSame('2021-10-20', $filter->value());
                    break;

                case 2:
                    self::assertSame('published_at', $filter->field());
                    self::assertTrue($filter->operator()->isEqual('lte'));
                    self::assertSame('2021-11-20', $filter->value());
                    break;
            }
        }
    }

    public function it_should_throw_an_exception_when_the_query_params_is_not_supported(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['it_is_not_supported' => 'foo']))->toQuery();
    }

    public function it_should_throw_an_exception_when_the_filter_is_not_supported(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['filter' => ['it_is_not_supported' => ['eq' => 'foo']]]))->toQuery();
    }

    public function it_should_throw_an_exception_when_the_filter_is_invalid(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['filter' => 'string_is_invalid']))->toQuery();
    }

    public function it_should_throw_an_exception_when_the_sort_is_not_supported(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['sort' => 'it_is_not_supported']))->toQuery();
    }

    public function it_should_throw_an_exception_when_the_sort_is_invalid(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['sort' => ['array_is_invalid']]))->toQuery();
    }

    /** @test */
    public function it_should_throw_an_exception_when_the_include_is_not_supported(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['include' => 'it_is_not_supported']))->toQuery();
    }

    /** @test */
    public function it_should_throw_an_exception_when_the_include_is_invalid(): void
    {
        $this->expectException(BadRequestException::class);

        (new FakeDocumentsRequest(['include' => ['array_is_invalid']]))->toQuery();
    }
}
