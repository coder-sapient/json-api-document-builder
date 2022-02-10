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

class InternalException extends JsonApiException
{
    /**
     * @return ErrorDocument
     */
    public function jsonApiErrors(): ErrorDocument
    {
        return new ErrorDocument(
            new Error(
                new Error\Title('Internal Server Error'),
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
        return '500';
    }
}
