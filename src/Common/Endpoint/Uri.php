<?php

namespace WebservicesNl\Common\Endpoint;

use Psr\Http\Message\UriInterface;

/**
 * Class Uri.
 *
 * Webservices PSR-7 UriInterface. This is a shameless copy from the Guzzle Uri
 *
 * @link https://github.com/guzzle/psr7/blob/master/src/Uri.php
 *
 * Terminology:
 * http://foo:bar@example.com:8042/over/there?name=ferret#nose
 * \__/   \______________________/\_________/ \_________/ \__/
 *  |               |                |            |        |
 * scheme         authority           path        query   fragment
 *
 */
class Uri implements UriInterface
{
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';
    /**
     * The fragment contains a fragment identifier providing direction to a secondary resource.
     *
     * Separated from the preceding part by a hash (#).  such as a section heading in an article identified by the
     * remainder of the URI.
     *
     * @var string
     */
    private $fragment = '';

    /**
     * Domain name of the URI (eg. example.com).
     *
     * @var string
     */
    private $host = '';

    /**
     * Contains data, usually organized in hierarchical form, that appears as a sequence of segments separated by slashes.
     *
     * @var string
     */
    private $path = '';

    /**
     * Retrieve the port component of the URI.
     *
     * @var int
     */
    private $port;

    /**
     * Separated from the preceding part by a question mark (?), containing a query string of non-hierarchical data.
     *
     * @var string
     */
    private $query = '';

    /**
     * Retrieve the scheme component of the URI.
     *
     * @var string
     */
    private $scheme = '';

    /**
     * Schemes.
     *
     * @var array
     */
    private static $schemes = [
        'ftp' => 21,
        'ssh' => 22,
        'http' => 80,
        'https' => 443,
    ];

    /**
     * Authentication section of the authority.
     *
     * Optional user name and password, separated by a colon, followed by an at symbol (@)
     *
     * @var string
     */
    private $userInfo = '';

    /**
     * @param string $uri URI to parse and wrap.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($uri = '')
    {
        if ($uri !== null) {
            $parts = parse_url($uri);
            if (!is_array($parts)) {
                throw new \InvalidArgumentException("Unable to parse URI: '$uri'");
            }
            $this->applyParts($parts);
        }
    }

    /**
     * Apply parse_url parts to a URI.
     *
     * @param $parts array of parse_url parts to apply.
     *
     * @throws \InvalidArgumentException
     */
    private function applyParts(array $parts = [])
    {
        $this->scheme = array_key_exists('scheme', $parts) ? $this->filterScheme($parts['scheme']) : null;
        $this->userInfo = array_key_exists('user', $parts) ? $parts['user'] : null;
        $this->host = array_key_exists('host', $parts) ? $parts['host'] : null;
        $this->port = array_key_exists('port', $parts) ? $this->filterPort($this->getScheme(), $this->getHost(), $parts['port']) : null;
        $this->path = array_key_exists('path', $parts) ? $this->filterPath($parts['path']) : null;
        $this->query = array_key_exists('query', $parts) ? $this->filterQueryAndFragment($parts['query']) : null;
        $this->fragment = array_key_exists('fragment', $parts) ? $this->filterQueryAndFragment($parts['fragment']) : null;
        $this->userInfo .= array_key_exists('pass', $parts) ? ':' . $parts['pass'] : null;
    }

    /**
     * Filter scheme.
     *
     * @param string $scheme
     *
     * @return string
     */
    private function filterScheme($scheme)
    {
        $scheme = strtolower($scheme);
        $scheme = rtrim($scheme, ':/');

        return $scheme;
    }

    /**
     * Filter port before setting it.
     *
     * @param string $scheme
     * @param string $host
     * @param int    $port
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException if the port is invalid.
     */
    private function filterPort($scheme, $host, $port)
    {
        if (null !== $port) {
            $port = (int) $port;
            if (1 > $port || 65535 < $port) {
                throw new \InvalidArgumentException(sprintf('Invalid port: %d. Must be between 1 and 65535', $port));
            }
        }

        return self::isStandardPort($port, (string) $scheme, (string) $host) ? null : $port;
    }

    /**
     * Is a given port standard for the current scheme?
     *
     * @param int    $port
     * @param string $scheme
     * @param string $host
     *
     * @return bool
     */
    private static function isStandardPort($port, $scheme = null, $host = null)
    {
        if ($host === null || $scheme === null) {
            return false;
        }

        return array_key_exists($scheme, static::$schemes) && $port === static::$schemes[$scheme];
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return string string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * {@inheritdoc}
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Filters the path of a URI.
     *
     * @param string $path
     *
     * @return string
     */
    private function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charSubDelims . ':@\/%]+|%(?![A-Fa-f\d]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the query string or fragment of a URI.
     *
     * @param string $str
     *
     * @return string
     */
    private function filterQueryAndFragment($str)
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charSubDelims . '%:@\/\?]+|%(?![A-Fa-f\d]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $str
        );
    }

    /**
     * Create a new URI with a specific query string value.
     *
     * Any existing query string values that exactly match the provided key are
     * removed and replaced with the given key value pair.
     *
     * Note: this function will convert "=" to "%3D" and "&" to "%26".
     *
     * @param UriInterface $uri   URI to use as base.
     * @param string       $key   Key to set.
     * @param string       $value Value to set.
     *
     * @throws \InvalidArgumentException
     *
     * @return Uri
     */
    public static function withQueryValue(UriInterface $uri, $key, $value)
    {
        $current = $uri->getQuery();

        $key = rawurlencode($key);
        $value = rawurlencode($value);

        $result = [];
        if ($current !== null) {
            foreach (explode('&', $current) as $part) {
                if (explode('=', $part)[0] !== $key) {
                    $result[] = $part;
                }
            }
        }

        $result[] = ($value !== '') ? $key . '=' . $value : $key;

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Resolve a base URI with a relative URI and return a new URI.
     *
     * @param UriInterface             $base Base URI
     * @param null|string|UriInterface $rel  Relative URI
     *
     * @throws \InvalidArgumentException
     *
     * @return UriInterface
     */
    public static function resolve(UriInterface $base, $rel = null)
    {
        if ($rel === null || $rel === '') {
            return $base;
        }

        if (!$rel instanceof UriInterface) {
            $rel = new self($rel);
        }

        // Return the relative uri as-is if it has a scheme.
        if ($rel->getScheme()) {
            return $rel->withPath(static::removeDotSegments($rel->getPath()));
        }

        $relParts = [
            'scheme'    => $rel->getScheme(),
            'authority' => $rel->getAuthority(),
            'path'      => $rel->getPath(),
            'query'     => $rel->getQuery(),
            'fragment'  => $rel->getFragment(),
        ];

        $parts = [
            'scheme'    => $base->getScheme(),
            'authority' => $base->getAuthority(),
            'path'      => $base->getPath(),
            'query'     => $base->getQuery(),
            'fragment'  => $base->getFragment(),
        ];

        if (!empty($relParts['path'])) {
            if (strpos($relParts['path'], '/') === 0) {
                $parts['path'] = self::removeDotSegments($relParts['path']);
                $parts['query'] = $relParts['query'];
                $parts['fragment'] = $relParts['fragment'];
            } else {
                $mergedPath = substr($parts['path'], 0, strrpos($parts['path'], '/') + 1);

                $parts['path'] = self::removeDotSegments($mergedPath . $relParts['path']);
                $parts['query'] = $relParts['query'];
                $parts['fragment'] = $relParts['fragment'];
            }
        } elseif (!empty($relParts['query'])) {
            $parts['query'] = $relParts['query'];
        } elseif ($relParts['fragment'] !== null) {
            $parts['fragment'] = $relParts['fragment'];
        }

        return new self(static::createUriString(
            $parts['scheme'],
            $parts['authority'],
            $parts['path'],
            $parts['query'],
            $parts['fragment']
        ));
    }

    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @param string $path
     *
     * @return string
     *
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    public static function removeDotSegments($path)
    {
        static $noopPaths = ['' => true, '/' => true, '*' => true];
        static $ignoreSegments = ['.' => true, '..' => true];

        if (array_key_exists($path, $noopPaths)) {
            return $path;
        }

        $results = [];
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($results);
            } elseif (!array_key_exists($segment, $ignoreSegments)) {
                $results[] = $segment;
            }
        }

        $newPath = implode('/', $results);
        // add the leading slash if necessary
        if (strpos($path, '/') === 0 && strpos($newPath, '/') !== 0) {
            $newPath = '/' . $newPath;
        }

        // add the trailing slash if necessary
        if ($newPath !== '/' && array_key_exists(end($segments), $ignoreSegments)) {
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * Create a new URI with a specific query string value removed.
     *
     * Any existing query string values that exactly match the provided key are removed.
     *
     * Note: this function will convert "=" to "%3D" and "&" to "%26".
     *
     * @param UriInterface $uri URI to use as a base.
     * @param string       $key Query string key value pair to remove.
     *
     * @throws \InvalidArgumentException
     *
     * @return UriInterface
     */
    public static function withoutQueryValue(UriInterface $uri, $key)
    {
        if ($uri->getQuery() === null) {
            return $uri;
        }

        $result = [];
        foreach (explode('&', $uri->getQuery()) as $part) {
            if (explode('=', $part)[0] !== $key) {
                $result[] = $part;
            }
        }

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Retrieve the port component of the URI.
     *
     * {@inheritdoc}
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $fragment
     *
     * @return UriInterface
     */
    public function withFragment($fragment)
    {
        if (strpos($fragment, '#') === 0) {
            $fragment = substr($fragment, 1);
        }
        $fragment = $this->filterQueryAndFragment($fragment);

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * @param string $query
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException
     */
    public function withQuery($query)
    {
        if (!is_string($query) && !method_exists($query, '__toString')) {
            throw new \InvalidArgumentException('Query string must be a string');
        }
        $query = (string) $query;
        if (strpos($query, '?') === 0) {
            $query = substr($query, 1);
        }
        $query = $this->filterQueryAndFragment($query);

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * Returns new instance with given host.
     *
     * @param string $host
     *
     * @return UriInterface
     */
    public function withHost($host)
    {
        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * Returns new instance with given port.
     *
     * @param int|null $port
     *
     * @throws \InvalidArgumentException
     *
     * @return UriInterface
     */
    public function withPort($port)
    {
        $port = $this->filterPort($this->scheme, $this->host, $port);

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * Returns instance with given path.
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException
     *
     * @return UriInterface
     */
    public function withPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Invalid path provided; must be a string');
        }
        $path = $this->filterPath($path);

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * Return new instance with the specified scheme.
     *
     * {@inheritdoc}
     *
     * @param string $scheme The scheme to use with the new instance.
     *
     * @throws \InvalidArgumentException
     *
     * @return UriInterface
     */
    public function withScheme($scheme)
    {
        $scheme = $this->filterScheme($scheme);

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->scheme, $new->host, $new->port);

        return $new;
    }

    /**
     * Returns new instance with given user info.
     *
     * @param string      $user
     * @param string|null $password
     *
     * @return UriInterface
     */
    public function withUserInfo($user, $password = null)
    {
        $info = $user;
        if ($password) {
            $info .= ':' . $password;
        }

        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     *
     * @return string
     */
    public function __toString()
    {
        return self::createUriString(
            $this->getScheme(),
            $this->getAuthority(),
            $this->getPath(),
            $this->getQuery(),
            $this->getFragment()
        );
    }

    /**
     * Create a URI string from its various parts.
     *
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return string
     */
    private static function createUriString(
        $scheme = null,
        $authority = null,
        $path = null,
        $query = null,
        $fragment = null
    ) {
        $uri = '';
        $hierPart = '';

        if ($scheme !== null) {
            $uri .= $scheme . ':';
        }

        if (!$authority !== '') {
            if ($scheme !== null && $scheme !== 'urn') {
                $hierPart .= '//';
            }
            $hierPart .= $authority;
        }

        if ($path !== null) {
            // Add a leading slash if necessary.
            if ($hierPart && strpos($path, '/') !== 0) {
                $hierPart .= '/';
            }
            $hierPart .= $path;
        }

        $uri .= $hierPart;

        // if query is present prepend ? sign
        if ($query !== null) {
            $uri .= '?' . $query;
        }

        // if fragment is present prepend # sign
        if ($fragment !== null) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $authority = $this->getHost();
        if ($authority === null) {
            return '';
        }

        if ($this->getUserInfo() !== '') {
            $authority = $this->getUserInfo() . '@' . $authority;
        }

        if ($this->port !== null && self::isStandardPort($this->port, $this->scheme, $this->host) === false) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * {@inheritdoc}
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path === null ? '' : $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * {@inheritdoc}
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param array $match
     *
     * @return string
     */
    private function rawurlencodeMatchZero(array $match)
    {
        return rawurlencode($match[0]);
    }
}
