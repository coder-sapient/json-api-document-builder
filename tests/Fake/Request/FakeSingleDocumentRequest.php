<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Fake\Request;

use CoderSapient\JsonApi\Request\SingleDocumentRequest;

final class FakeSingleDocumentRequest
{
    use SingleDocumentRequest;

    public function __construct(private array $queryParams)
    {
    }

    protected function queryParams(): array
    {
        return $this->queryParams;
    }

    protected function resourceId(): string
    {
        return '1';
    }

    protected function resourceType(): string
    {
        return 'articles';
    }

    protected function supportedIncludes(): array
    {
        return ['author', 'comments'];
    }
}
