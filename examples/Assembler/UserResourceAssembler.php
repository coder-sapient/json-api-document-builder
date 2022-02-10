<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Assembler;

use CoderSapient\JsonApi\Examples\Model\User;
use JsonApiPhp\JsonApi\Attribute;
use JsonApiPhp\JsonApi\ResourceObject;

final class UserResourceAssembler
{
    /**
     * @return ResourceObject[]
     */
    public function toResources(User ...$users): array
    {
        $collect = [];

        foreach ($users as $user) {
            $collect[] = $this->toResource($user);
        }

        return $collect;
    }

    public function toResource(User $user): ResourceObject
    {
        return new ResourceObject(
            'users',
            $user->id(),
            new Attribute('name', $user->name()),
        );
    }
}
