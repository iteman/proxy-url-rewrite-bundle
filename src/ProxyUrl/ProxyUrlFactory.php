<?php
/*
 * Copyright (c) 2014-2015 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsProxyURLRewriteBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\ProxyURLRewriteBundle\ProxyUrl;

use PHPMentors\DomainKata\Service\ServiceInterface;

class ProxyUrlFactory implements ServiceInterface
{
    /**
     * @param int|string $proxyUrlId
     * @param string     $path
     * @param string     $proxyUrl
     *
     * @return ProxyUrl
     */
    public function create($proxyUrlId, $path, $proxyUrl)
    {
        list($proxyUrlPath, $proxyUrlHost, $proxyUrlScheme, $proxyUrlPort) = static::parseUrl($proxyUrl);

        return new ProxyUrl($proxyUrlId, $path, $proxyUrlPath, $proxyUrlHost, $proxyUrlScheme, $proxyUrlPort);
    }

    /**
     * @param string $url
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     *
     * @link http://php.net/manual/en/function.parse-url.php
     */
    public static function parseUrl($url)
    {
        $components = parse_url($url);
        if ($components === false) {
            throw new \UnexpectedValueException(sprintf('The proxy URL "%s" is malformed.', $url));
        }

        $path = array_key_exists('path', $components) ? $components['path'] : null;
        $host = array_key_exists('host', $components) ? $components['host'] : null;
        $scheme = array_key_exists('scheme', $components) ? $components['scheme'] : null;
        $port = array_key_exists('port', $components) ? $components['port'] : null;

        if (strpos($path, '//') === 0) {
            $endOfHostPosition = strpos($path, '/', 2);
            if ($endOfHostPosition === false) {
                $host = substr($path, 2);
                $path = null;
            } elseif ($endOfHostPosition == 2) {
                throw new \UnexpectedValueException(sprintf('The proxy URL "%s" is malformed.', $url));
            } elseif ($endOfHostPosition > 2) {
                $host = substr($path, 2, $endOfHostPosition - 2);
                $path = substr($path, $endOfHostPosition);
            }
        }

        return array($path, $host, $scheme, $port);
    }
}
