<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Exception;

class ResourceResolverNotFoundException extends InternalException
{
    /**
     * @param string $resourceType
     */
    public function __construct(private string $resourceType)
    {
        parent::__construct("Resource resolver for type [{$resourceType}] is not registered");
    }

    /**
     * @return string
     */
    public function resourceType(): string
    {
        return $this->resourceType;
    }
}
