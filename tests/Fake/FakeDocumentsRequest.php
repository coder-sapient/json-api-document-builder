<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Fake;

use CoderSapient\JsonApi\Request\DocumentsRequest;

final class FakeDocumentsRequest
{
    use DocumentsRequest;

    public function __construct(private array $queryParams)
    {
    }

    protected function queryParams(): array
    {
        return $this->queryParams;
    }

    protected function resourceType(): string
    {
        return 'articles';
    }

    protected function supportedFilters(): array
    {
        return [
            'title' => ['like', 'eq'],
            'published_at' => ['gte', 'lte', 'gt', 'lt'],
        ];
    }

    protected function supportedSorting(): array
    {
        return ['title', 'published_at'];
    }

    protected function supportedIncludes(): array
    {
        return ['author', 'comments'];
    }
}
