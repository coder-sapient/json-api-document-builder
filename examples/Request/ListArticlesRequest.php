<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Request;

use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Examples\ResourceTypes;
use CoderSapient\JsonApi\Request\DocumentsRequest;

final class ListArticlesRequest extends Request
{
    use DocumentsRequest;

    public function resourceType(): string
    {
        return ResourceTypes::ARTICLES;
    }

    public function acceptableIncludes(): array
    {
        return ['author'];
    }

    public function acceptableSorting(): array
    {
        return ['title'];
    }

    public function acceptableFilters(): array
    {
        return [
            'author_id' => [FilterOperator::EQUAL],
            'title' => [FilterOperator::EQUAL, FilterOperator::LIKE],
        ];
    }
}
