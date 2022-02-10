<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Exception;

use JsonApiPhp\JsonApi\Error;
use JsonApiPhp\JsonApi\ErrorDocument;

class ResourceNotFoundException extends JsonApiException
{
    /**
     * @param string $key
     */
    public function __construct(private string $key)
    {
        parent::__construct("Not found [{$key}]");
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return ErrorDocument
     */
    public function jsonApiErrors(): ErrorDocument
    {
        return new ErrorDocument(
            new Error(
                new Error\Title('Resource Not Found'),
                new Error\Status($this->jsonApiStatus()),
                new Error\Detail($this->getMessage()),
            ),
        );
    }

    /**
     * @return string
     */
    public function jsonApiStatus(): string
    {
        return '404';
    }
}
