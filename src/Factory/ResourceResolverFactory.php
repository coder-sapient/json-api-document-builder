<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Factory;

use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Resolver\ResourceResolver;

interface ResourceResolverFactory
{
    /**
     * @param string $resourceType
     *
     * @return ResourceResolver
     *
     * @throws ResourceResolverNotFoundException
     */
    public function make(string $resourceType): ResourceResolver;
}
