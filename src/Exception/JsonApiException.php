<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Exception;

use Exception;
use JsonApiPhp\JsonApi\ErrorDocument;

abstract class JsonApiException extends Exception
{
    /**
     * @return ErrorDocument
     */
    abstract public function jsonApiErrors(): ErrorDocument;

    /**
     * @return string
     */
    abstract public function jsonApiStatus(): string;
}
