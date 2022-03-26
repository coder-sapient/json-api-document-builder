<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi;

final class Utils
{
    /**
     * @param string $key
     *
     * @return string
     *
     * @see \JsonApiPhp\JsonApi\ResourceObject::key()
     */
    public static function getType(string $key): string
    {
        return self::splitKey($key)[0];
    }

    /**
     * @param string $key
     *
     * @return array ['type', 'id']
     *
     * @see \JsonApiPhp\JsonApi\ResourceObject::key()
     */
    public static function splitKey(string $key): array
    {
        return explode(':', $key);
    }

    /**
     * @param string $string
     * @param string $needle
     *
     * @return string
     */
    public static function subStrFirst(string $string, string $needle): string
    {
        return $needle === $string[0] ? mb_substr($string, 1) : $string;
    }

    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return array
     */
    public static function explodeIfNotEmpty(string $string, string $delimiter = ','): array
    {
        return '' !== $string ? explode($delimiter, $string) : [];
    }

    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return array|string
     */
    public static function explodeIfContains(string $string, string $delimiter = ','): array|string
    {
        return str_contains($string, $delimiter) ? explode($delimiter, $string) : $string;
    }
}
