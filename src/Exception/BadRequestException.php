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

class BadRequestException extends JsonApiException
{
    /**
     * @param string $message
     * @param string $source
     */
    public function __construct(string $message, private string $source)
    {
        parent::__construct($message);
    }

    /**
     * @return ErrorDocument
     */
    public function jsonApiErrors(): ErrorDocument
    {
        return new ErrorDocument(
            new Error(
                new Error\Title('Bad Request'),
                new Error\Status($this->jsonApiStatus()),
                new Error\Detail($this->message),
                new Error\SourceParameter($this->source),
            ),
        );
    }

    /**
     * @return string
     */
    public function jsonApiStatus(): string
    {
        return '400';
    }
}
