<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Criteria\Includes;

class SingleDocumentQuery
{
    public function __construct(
        private string $resourceId,
        private string $resourceType,
        private Includes $includes,
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
        return $this->includes;
    }
}
