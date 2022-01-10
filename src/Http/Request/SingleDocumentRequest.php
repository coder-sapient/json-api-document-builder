<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Http\Request;

use CoderSapient\JsonApi\Document\Builder\SingleDocumentQuery;

trait SingleDocumentRequest
{
    use JsonApiRequest;

    public function toQuery(): SingleDocumentQuery
    {
        $this->ensureQueryParametersIsValid();

        return new SingleDocumentQuery(
            $this->resourceId(),
            $this->resourceType(),
            $this->includes(),
        );
    }

    abstract protected function resourceId(): string;

    protected function supportedQueryParams(): array
    {
        return [$this->queryInclude];
    }
}
