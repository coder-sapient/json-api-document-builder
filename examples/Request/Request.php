<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Request;

use Psr\Http\Message\ServerRequestInterface;

abstract class Request
{
    public function __construct(protected ServerRequestInterface $request)
    {
    }

    public function queryParams(): array
    {
        return $this->request->getQueryParams();
    }
}
