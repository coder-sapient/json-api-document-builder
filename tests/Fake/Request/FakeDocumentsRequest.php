<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Fake\Request;

use CoderSapient\JsonApi\Request\DocumentsRequest;

final class FakeDocumentsRequest
{
    use DocumentsRequest;

    public function __construct(private array $queryParams)
    {
    }

    public function queryParams(): array
    {
        return $this->queryParams;
    }

    public function resourceType(): string
    {
        return 'articles';
    }

    public function acceptableFilters(): array
    {
        return [
            'title' => ['like', 'eq'],
            'published_at' => ['gte', 'lte', 'gt', 'lt'],
        ];
    }

    public function acceptableSorting(): array
    {
        return ['title', 'published_at'];
    }

    public function acceptableIncludes(): array
    {
        return ['author', 'comments'];
    }
}
