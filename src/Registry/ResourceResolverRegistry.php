<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Registry;

use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Resolver\ResourceResolver;

interface ResourceResolverRegistry
{
    /**
     * @param string $resourceType
     *
     * @return ResourceResolver
     *
     * @throws ResourceResolverNotFoundException
     */
    public function get(string $resourceType): ResourceResolver;
}
