<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Criteria\Includes;

class SingleDocumentQuery
{
    private ?Includes $includes = null;

    public function __construct(
        private string $resourceId,
        private string $resourceType,
    ) {
    }

    public function resourceId(): string
    {
        return $this->resourceId;
    }

    public function resourceType(): string
    {
        return $this->resourceType;
    }

    public function includes(): Includes
    {
        return $this->includes ?? new Includes();
    }

    public function setIncludes(Includes $includes): self
    {
        $this->includes = $includes;

        return $this;
    }
}
