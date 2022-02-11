<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Request;

use CoderSapient\JsonApi\Document\Query\SingleDocumentQuery;
use CoderSapient\JsonApi\Exception\BadRequestException;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;

trait SingleDocumentRequest
{
    use JsonApiRequest;

    /**
     * @return string
     */
    abstract public function resourceId(): string;

    /**
     * @return SingleDocumentQuery
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function toQuery(): SingleDocumentQuery
    {
        $this->ensureQueryParamsIsValid();

        $query = new SingleDocumentQuery($this->resourceId(), $this->resourceType());

        return $query->setIncludes($this->includes());
    }

    /**
     * @return array
     */
    public function acceptableQueryParams(): array
    {
        return [$this->queryInclude];
    }
}
