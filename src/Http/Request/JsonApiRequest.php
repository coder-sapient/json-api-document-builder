<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Http\Request;

use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Exception\BadRequestException;
use CoderSapient\JsonApi\Utils;

trait JsonApiRequest
{
    protected string $queryInclude = 'include';

    protected string $includeDelimiter = ',';

    abstract protected function queryParams(): array;

    abstract protected function resourceType(): string;

    protected function queryParam(string $name, mixed $default = null): mixed
    {
        return $this->queryParams()[$name] ?? $default;
    }

    protected function supportedQueryParams(): array
    {
        return [];
    }

    /**
     *  ['foo', 'bar', 'foo.bar'].
     */
    protected function supportedIncludes(): array
    {
        return [];
    }

    protected function includes(): Includes
    {
        $includes = $this->queryParam($this->queryInclude, '');

        $this->ensureIncludeIsValid($includes);

        return new Includes(Utils::explodeIfNotEmpty($includes), $this->includeDelimiter);
    }

    protected function ensureIncludeIsValid($include): void
    {
        $this->ensureQueryParamIsString($this->queryInclude, $include);

        $include = Utils::explodeIfNotEmpty($include, $this->includeDelimiter);

        $this->ensureQueryParamIsSupported($this->queryInclude, $include, $this->supportedIncludes());
    }

    protected function ensureQueryParamsIsValid(): void
    {
        foreach ($this->queryParams() as $param => $value) {
            if (
                ! preg_match('/[^a-z]/', $param)
                && ! in_array($param, $this->supportedQueryParams(), true)
            ) {
                $this->throwBadRequestException("Invalid query parameter [{$param}]", $param);
            }
        }
    }

    protected function ensureQueryParamIsString(string $param, mixed $value): void
    {
        if (! is_string($value)) {
            $this->throwBadRequestException("Must be a string [?{$param}=value]", $param);
        }
    }

    protected function ensureQueryParamIsArray(string $param, mixed $value): void
    {
        if (! is_array($value)) {
            $this->throwBadRequestException('Must be an compound [?' . $param . '[field]=value]', $param);
        }
    }

    protected function ensureQueryParamIsSupported(string $param, array $given, array $allowed): void
    {
        if ([] !== $diff = array_diff($given, $allowed)) {
            $this->throwBadRequestException(
                "Not allowed to {$param} [" . implode(',', $diff) . ']',
                $param,
            );
        }
    }

    protected function throwBadRequestException(string $message, string $parameter): void
    {
        throw new BadRequestException($message, $parameter);
    }
}
