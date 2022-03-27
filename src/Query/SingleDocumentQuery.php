<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Query;

use CoderSapient\JsonApi\Utils;

class SingleDocumentQuery extends JsonApiQuery
{
    /**
     * @param string $resourceId
     * @param string $resourceType
     */
    public function __construct(private string $resourceId, private string $resourceType)
    {
    }

    /**
     * @return string
     */
    public function resourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @return string
     */
    public function resourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @return string
     */
    public function toHash(): string
    {
        return md5(Utils::compositeKey($this->resourceType(), $this->resourceId()));
    }
}
