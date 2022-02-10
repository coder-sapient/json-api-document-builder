<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Document\Query;

use CoderSapient\JsonApi\Criteria\Includes;

abstract class JsonApiQuery
{
    /**
     * @var Includes|null
     */
    private ?Includes $includes = null;

    /**
     * @return string
     */
    abstract public function resourceType(): string;

    /**
     * @return string
     */
    abstract public function serialize(): string;

    /**
     * @return Includes
     */
    public function includes(): Includes
    {
        return $this->includes ?? new Includes();
    }

    /**
     * @param Includes $includes
     *
     * @return $this
     */
    public function setIncludes(Includes $includes): self
    {
        $this->includes = $includes;

        return $this;
    }
}
