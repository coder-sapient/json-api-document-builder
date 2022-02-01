<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Request;

use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Examples\ResourceTypes;
use CoderSapient\JsonApi\Request\DocumentsRequest;

final class ListArticlesRequest extends Request
{
    use DocumentsRequest;

    protected function resourceType(): string
    {
        return ResourceTypes::ARTICLES;
    }

    protected function supportedIncludes(): array
    {
        return ['author'];
    }

    protected function supportedSorting(): array
    {
        return ['title'];
    }

    protected function supportedFilters(): array
    {
        return [

            'author_id' => [FilterOperator::EQUAL],
            'title' => [FilterOperator::EQUAL, FilterOperator::LIKE],
        ];
    }
}
