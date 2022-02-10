<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Document\Resolver;

use CoderSapient\JsonApi\Document\Query\DocumentsQuery;
use CoderSapient\JsonApi\Document\Resolver\Response\PaginationResponse;

interface PaginationResolver
{
    /**
     * @param DocumentsQuery $query
     *
     * @return PaginationResponse
     */
    public function paginate(DocumentsQuery $query): PaginationResponse;
}
