<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Cache;

use CoderSapient\JsonApi\Exception\BadRequestException;
use CoderSapient\JsonApi\Exception\InternalException;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Tests\Assert\AssertDocumentEquals;
use PHPUnit\Framework\TestCase;

final class ExceptionsTest extends TestCase
{
    use AssertDocumentEquals;

    /** @test */
    public function it_should_create_a_valid_bad_request_exception(): void
    {
        $exception = new BadRequestException('Filter is invalid', 'filter');

        self::assertSame('400', $exception->jsonApiStatus());
        self::assertEncodesTo(
            '
            {
               "errors": [
                    {
                        "title": "Bad Request",
                        "status": "400",
                        "detail": "Filter is invalid",
                        "source": {
                            "parameter": "filter"
                         }
                    }
               ]
            }
            ',
            $exception->jsonApiErrors(),
        );
    }

    /** @test */
    public function it_should_create_a_valid_resource_not_found_exception(): void
    {
        $exception = new ResourceNotFoundException('articles:42');

        self::assertSame('articles:42', $exception->key());
        self::assertSame('404', $exception->jsonApiStatus());
        self::assertEncodesTo(
            '
            {
               "errors": [
                    {
                        "title": "Resource Not Found",
                        "status": "404",
                        "detail": "Not found [articles:42]"
                    }
               ]
            }
            ',
            $exception->jsonApiErrors(),
        );
    }

    /** @test */
    public function it_should_create_a_valid_resource_resolver_not_found_exception(): void
    {
        $exception = new ResourceResolverNotFoundException('articles');

        self::assertSame('articles', $exception->resourceType());
        self::assertSame('500', $exception->jsonApiStatus());
        self::assertEncodesTo(
            '
            {
               "errors": [
                    {
                        "title": "Internal Server Error",
                        "status": "500",
                        "detail": "Resource resolver for type [articles] is not registered"
                    }
               ]
            }
            ',
            $exception->jsonApiErrors(),
        );
    }

    /** @test */
    public function it_should_create_a_valid_internal_exception(): void
    {
        $exception = new InternalException('Exception detail...');

        self::assertSame('500', $exception->jsonApiStatus());
        self::assertEncodesTo(
            '
            {
               "errors": [
                    {
                        "title": "Internal Server Error",
                        "status": "500",
                        "detail": "Exception detail..."
                    }
               ]
            }
            ',
            $exception->jsonApiErrors(),
        );
    }
}
