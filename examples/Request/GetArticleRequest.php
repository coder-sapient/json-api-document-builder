<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Request;

use CoderSapient\JsonApi\Examples\ResourceTypes;
use CoderSapient\JsonApi\Http\Request\SingleDocumentRequest;

final class GetArticleRequest extends Request
{
    use SingleDocumentRequest;

    public function resourceId(): string
    {
        return '1'; // /article/{id}
    }

    public function resourceType(): string
    {
        return ResourceTypes::ARTICLES;
    }

    public function supportedIncludes(): array
    {
        return ['author'];
    }
}
