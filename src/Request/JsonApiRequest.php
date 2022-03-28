<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Request;

use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Exception\BadRequestException;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Utils;

trait JsonApiRequest
{
    /** @var string  */
    protected string $queryInclude = 'include';

    /** @var string  */
    protected string $includeDelimiter = ',';

    /** @var string */
    protected string $includeRelationDelimiter = '.';

    /**
     * @return array
     */
    abstract public function queryParams(): array;

    /**
     * @return string
     */
    abstract public function resourceType(): string;

    /**
     * @param string $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function queryParam(string $name, mixed $default = null): mixed
    {
        return $this->queryParams()[$name] ?? $default;
    }

    /**
     * @return array
     */
    public function acceptableQueryParams(): array
    {
        return [];
    }

    /**
     * Example: ['comments', 'comments.user'].
     *
     * @return array
     */
    public function acceptableIncludes(): array
    {
        return [];
    }

    /**
     * @return Includes
     *
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function includes(): Includes
    {
        $includes = $this->queryParam($this->queryInclude, '');

        $this->ensureIncludeIsValid($includes);

        return new Includes(
            Utils::explodeIfNotEmpty($includes, $this->includeDelimiter),
            $this->includeRelationDelimiter,
        );
    }

    /**
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureQueryParamsIsValid(): void
    {
        foreach ($this->queryParams() as $param => $value) {
            if (! in_array($param, $this->acceptableQueryParams(), true)) {
                $this->throwBadRequestException(
                    sprintf('Invalid query parameter [%s]', $param),
                    $param,
                );
            }
        }
    }

    /**
     * @param mixed $include
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureIncludeIsValid(mixed $include): void
    {
        $this->ensureQueryParamIsString($this->queryInclude, $include);

        $include = Utils::explodeIfNotEmpty($include, $this->includeDelimiter);

        $this->ensureQueryParamValueIsAcceptable($this->queryInclude, $include, $this->acceptableIncludes());
    }

    /**
     * @param string $param
     * @param mixed $value
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureQueryParamIsString(string $param, mixed $value): void
    {
        if (! is_string($value)) {
            $this->throwBadRequestException(
                sprintf('%s must be a string [%s=value]', $param, $param),
                $param,
            );
        }
    }

    /**
     * @param string $param
     * @param mixed $value
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureQueryParamIsArray(string $param, mixed $value): void
    {
        if (! is_array($value)) {
            $this->throwBadRequestException(
                sprintf('%s must be a compound [%s[key]=value]', $param, $param),
                $param,
            );
        }
    }

    /**
     * @param string $param
     * @param mixed $value
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureQueryParamIsPositiveInt(string $param, mixed $value): void
    {
        if (! is_numeric($value) || (int) $value < 1) {
            $this->throwBadRequestException(
                sprintf('%s must be a positive integer', $param),
                $param,
            );
        }
    }

    /**
     * @param string $param
     * @param array $given
     * @param array $acceptable
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function ensureQueryParamValueIsAcceptable(string $param, array $given, array $acceptable): void
    {
        if ([] !== $diff = array_diff($given, $acceptable)) {
            $this->throwBadRequestException(
                sprintf('Not allowed to `%s` [%s]', $param, implode(',', $diff)),
                $param,
            );
        }
    }

    /**
     * @param string $message
     * @param string $parameter
     *
     * @return void
     *
     * @throws BadRequestException
     */
    protected function throwBadRequestException(string $message, string $parameter): void
    {
        throw new BadRequestException($message, $parameter);
    }
}
