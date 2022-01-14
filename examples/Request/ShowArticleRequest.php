<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Request;

use CoderSapient\JsonApi\Examples\ResourceTypes;
use CoderSapient\JsonApi\Request\SingleDocumentRequest;

final class ShowArticleRequest extends Request
{
    use SingleDocumentRequest;

    protected function resourceId(): string
    {
        return '1'; // ~/articles/{resourceId}
    }

    protected function resourceType(): string
    {
        return ResourceTypes::ARTICLES;
    }

    protected function supportedIncludes(): array
    {
        return ['author'];
    }
}
