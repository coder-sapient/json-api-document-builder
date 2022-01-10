<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Request;

use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Examples\ResourceTypes;
use CoderSapient\JsonApi\Http\Request\DocumentsRequest;

final class GetArticlesRequest extends Request
{
    use DocumentsRequest;

    public function resourceType(): string
    {
        return ResourceTypes::ARTICLES;
    }

    public function supportedIncludes(): array
    {
        return ['author'];
    }

    public function supportedSorting(): array
    {
        return ['title'];
    }

    public function supportedFilters(): array
    {
        return [
            'title' => [FilterOperator::EQUAL, FilterOperator::LIKE],
            'author_id' => [FilterOperator::EQUAL],
        ];
    }
}
