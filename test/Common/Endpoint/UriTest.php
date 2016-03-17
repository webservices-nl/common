<?php

namespace WebservicesNl\Test\Common\Endpoint;

use WebservicesNl\Common\Endpoint\Uri;

/**
 * Class UriTest.
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    const RFC3986_BASE = "http://a/b/c/d;p?q";

    /**
     *
     */
    public function testParsesProvidedUrl()
    {
        $uri = new Uri('https://johndoe:secret@mydomain.com:443/path/123?q=abc#test');

        // Standard port 443 for https gets ignored.
        self::assertEquals('https://johndoe:secret@mydomain.com/path/123?q=abc#test', (string)$uri);
        self::assertEquals('test', $uri->getFragment());
        self::assertEquals('mydomain.com', $uri->getHost());
        self::assertEquals('/path/123', $uri->getPath());
        self::assertEquals(null, $uri->getPort());
        self::assertEquals('q=abc', $uri->getQuery());
        self::assertEquals('https', $uri->getScheme());
        self::assertEquals('johndoe:secret', $uri->getUserInfo());
    }

    public function testChangePort()
    {
        $uri = new Uri('https://johndoe:secret@mydomain.com:1234');
        self::assertEquals(1234, $uri->getPort());

        $newUri = $uri->withPort(5678);
        self::assertEquals(5678, $newUri->getPort());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to parse URI
     */
    public function testValidatesUriCanBeParsed()
    {
        new Uri('///');
    }

    /**
     *
     */
    public function testCanTransformAndRetrievePartsIndividually()
    {
        $uri = (new Uri(''))
            ->withFragment('#test')
            ->withHost('example.com')
            ->withPath('path/123')
            ->withPort(8080)
            ->withQuery('?q=abc')
            ->withScheme('http')
            ->withUserInfo('user', 'pass');

        // Test getters.
        self::assertEquals('user:pass@example.com:8080', $uri->getAuthority());
        self::assertEquals('test', $uri->getFragment());
        self::assertEquals('example.com', $uri->getHost());
        self::assertEquals('path/123', $uri->getPath());
        self::assertEquals(8080, $uri->getPort());
        self::assertEquals('q=abc', $uri->getQuery());
        self::assertEquals('http', $uri->getScheme());
        self::assertEquals('user:pass', $uri->getUserInfo());
    }

    /**
     *
     */
    public function testWithUserInfo()
    {
        $uri = (new Uri(''))
            ->withFragment('#test')
            ->withHost('example.com')
            ->withPath('path/123')
            ->withPort(8080)
            ->withQuery('?q=abc')
            ->withScheme('http')
            ->withUserInfo('user', 'pass');

        self::assertEquals('user:pass', $uri->withUserInfo('user:pass')->getUserInfo());
        self::assertEquals('another:pass2', $uri->withUserInfo('another:pass2')->getUserInfo());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid port: 100000. Must be between 1 and 65535
     */
    public function testPortMustBeLowerThan()
    {
        (new Uri(''))->withPort(100000);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid port: 0. Must be between 1 and 65535
     */
    public function testPortMustBeGreaterThan()
    {
        (new Uri(''))->withPort(0);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPathMustBeValid()
    {
        (new Uri(''))->withPath([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testQueryMustBeValid()
    {
        (new Uri(''))->withQuery(new \stdClass);
    }

    /**
     *
     */
    public function testAllowsFalseyUrlParts()
    {
        $url = new Uri('http://a:1/0?0#0');
        self::assertSame('a', $url->getHost());
        self::assertEquals(1, $url->getPort());
        self::assertSame('/0', $url->getPath());
        self::assertEquals('0', (string)$url->getQuery());
        self::assertSame('0', $url->getFragment());
        self::assertEquals('http://a:1/0?0#0', (string)$url);

        $url = new Uri('');
        self::assertSame('', (string)$url);

        $url = new Uri('0');
        self::assertSame('0', (string)$url);

        $url = new Uri('/');
        self::assertSame('/', (string)$url);
    }

    /**
     * @dataProvider getResolveTestCases
     *
     * @param string $base base Url
     * @param string $rel new relative path
     * @param string $expected output
     */
    public function testResolvesUris($base, $rel, $expected)
    {
        $uri = new Uri($base);
        $actual = Uri::resolve($uri, $rel);
        self::assertEquals($expected, (string)$actual);
    }

    /**
     * @return array
     */
    public function getResolveTestCases()
    {
        return [
//            [self::RFC3986_BASE, 'g:h',           'g:h'],
            [self::RFC3986_BASE, 'g', 'http://a/b/c/g'],
            [self::RFC3986_BASE, './g', 'http://a/b/c/g'],
            [self::RFC3986_BASE, 'g/', 'http://a/b/c/g/'],
            [self::RFC3986_BASE, '/g', 'http://a/g'],
            [self::RFC3986_BASE, '?y', 'http://a/b/c/d;p?y'],
            [self::RFC3986_BASE, 'g?y', 'http://a/b/c/g?y'],
            [self::RFC3986_BASE, '#s', 'http://a/b/c/d;p?q#s'],
            [self::RFC3986_BASE, 'g#s', 'http://a/b/c/g#s'],
            [self::RFC3986_BASE, 'g?y#s', 'http://a/b/c/g?y#s'],
            [self::RFC3986_BASE, ';x', 'http://a/b/c/;x'],
            [self::RFC3986_BASE, 'g;x', 'http://a/b/c/g;x'],
            [self::RFC3986_BASE, 'g;x?y#s', 'http://a/b/c/g;x?y#s'],
            [self::RFC3986_BASE, '', self::RFC3986_BASE],
            [self::RFC3986_BASE, '.', 'http://a/b/c/'],
            [self::RFC3986_BASE, './', 'http://a/b/c/'],
            [self::RFC3986_BASE, '..', 'http://a/b/'],
            [self::RFC3986_BASE, '../', 'http://a/b/'],
            [self::RFC3986_BASE, '../g', 'http://a/b/g'],
            [self::RFC3986_BASE, '../..', 'http://a/'],
            [self::RFC3986_BASE, '../../', 'http://a/'],
            [self::RFC3986_BASE, 'http://a', 'http://a/'],
            [self::RFC3986_BASE, '../../g', 'http://a/g'],
            [self::RFC3986_BASE, '../../../g', 'http://a/g'],
            [self::RFC3986_BASE, '../../../../g', 'http://a/g'],
            [self::RFC3986_BASE, '/./g', 'http://a/g'],
            [self::RFC3986_BASE, '/../g', 'http://a/g'],
            [self::RFC3986_BASE, 'g.', 'http://a/b/c/g.'],
            [self::RFC3986_BASE, '.g', 'http://a/b/c/.g'],
            [self::RFC3986_BASE, 'g..', 'http://a/b/c/g..'],
            [self::RFC3986_BASE, '..g', 'http://a/b/c/..g'],
            [self::RFC3986_BASE, './../g', 'http://a/b/g'],
            [self::RFC3986_BASE, 'foo////g', 'http://a/b/c/foo////g'],
            [self::RFC3986_BASE, './g/.', 'http://a/b/c/g/'],
            [self::RFC3986_BASE, 'g/./h', 'http://a/b/c/g/h'],
            [self::RFC3986_BASE, 'g/../h', 'http://a/b/c/h'],
            [self::RFC3986_BASE, 'g;x=1/./y', 'http://a/b/c/g;x=1/y'],
            [self::RFC3986_BASE, 'g;x=1/../y', 'http://a/b/c/y'],
            ['http://u@a/b/c/d;p?q', '.', 'http://u@a/b/c/'],
            ['http://u:p@a/b/c/d;p?q', '.', 'http://u:p@a/b/c/'],
            ['http://a/b/c/d/', 'e', 'http://a/b/c/d/e'],
        ];
    }

    /**
     *
     */
    public function testAddAndRemoveQueryValues()
    {
        $uri = new Uri('http://foo.com/bar');
        $uri = Uri::withoutQueryValue($uri, 'x');
        $uri = Uri::withQueryValue($uri, 'a', 'b');
        $uri = Uri::withQueryValue($uri, 'c', 'd');
        $uri = Uri::withQueryValue($uri, 'e', null);
        self::assertEquals('a=b&c=d&e', $uri->getQuery());

        $uri = Uri::withoutQueryValue($uri, 'x');
        $uri = Uri::withoutQueryValue($uri, 'c');
        $uri = Uri::withoutQueryValue($uri, 'e');
        self::assertEquals('a=b', $uri->getQuery());

        $uri = Uri::withoutQueryValue($uri, 'a');
        $uri = Uri::withoutQueryValue($uri, 'a');
        self::assertEquals('', $uri->getQuery());
    }

    /**
     *
     */
    public function testGetAuthorityReturnsCorrectPort()
    {
        // HTTPS non-standard port
        $uri = new Uri('https://foo.co:99');
        self::assertEquals('foo.co:99', $uri->getAuthority());

        // HTTP non-standard port
        $uri = new Uri('http://foo.co:99');
        self::assertEquals('foo.co:99', $uri->getAuthority());

        // No scheme
        $uri = new Uri('foo.co:99');
        self::assertEquals('foo.co:99', $uri->getAuthority());

        // No host or port
        $uri = new Uri('http:');
        self::assertEquals('', $uri->getAuthority());

        // No host or port
        $uri = new Uri('http://foo.co');
        self::assertEquals('foo.co', $uri->getAuthority());
    }

    /**
     * @return array
     */
    public function pathTestProvider()
    {
        return [
            // Percent encode spaces.
            ['http://foo.com/baz bar', 'http://foo.com/baz%20bar'],

            // Don't encode something that's already encoded.
            ['http://foo.com/baz%20bar', 'http://foo.com/baz%20bar'],

            // Percent encode invalid percent encodings
            ['http://foo.com/baz%2-bar', 'http://foo.com/baz%252-bar'],

            // Don't encode path segments
            ['http://foo.com/baz/bar/bam?a', 'http://foo.com/baz/bar/bam?a'],
            ['http://foo.com/baz+bar', 'http://foo.com/baz+bar'],
            ['http://foo.com/baz:bar', 'http://foo.com/baz:bar'],
            ['http://foo.com/baz@bar', 'http://foo.com/baz@bar'],
            ['http://foo.com/baz(bar);bam/', 'http://foo.com/baz(bar);bam/'],
            ['http://foo.com/a-zA-Z0-9.-_~!$&\'()*+,;=:@', 'http://foo.com/a-zA-Z0-9.-_~!$&\'()*+,;=:@'],
        ];
    }

    /**
     * @dataProvider pathTestProvider
     *
     * @param string $input
     * @param string $output
     */
    public function testUriEncodesPathProperly($input, $output)
    {
        $uri = new Uri($input);
        self::assertEquals($output, (string)$uri);
    }

    /**
     *
     */
    public function testDoesNotAddPortWhenNoPort()
    {
        self::assertEquals('bar', (new Uri('//bar'))->getHost());
    }

    /**
     *
     */
    public function testAllowsForRelativeUri()
    {
        $uri = (new Uri)->withPath('foo');
        self::assertEquals('foo', $uri->getPath());
        self::assertEquals('foo', (string)$uri);
    }

    /**
     *
     */
    public function testAddsSlashForRelativeUriStringWithHost()
    {
        $uri = (new Uri)->withPath('foo')->withHost('bar.com');
        self::assertEquals('foo', $uri->getPath());
        self::assertEquals('bar.com/foo', (string)$uri);
    }

    /**
     * @dataProvider pathTestNoAuthority
     *
     * @param string $input
     */
    public function testNoAuthority($input)
    {
        $uri = new Uri($input);
        self::assertEquals($input, (string)$uri);
    }

    /**
     * @return array
     */
    public function pathTestNoAuthority()
    {
        return [
            // path-rootless
            ['urn:example:animal:ferret:nose'],
            // path-absolute
            ['urn:/example:animal:ferret:nose'],
            ['urn:/'],
            // path-empty
            ['urn:'],
            ['urn'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to parse URI
     */
    public function testNoAuthorityWithInvalidPath()
    {
        $input = 'urn://example:animal:ferret:nose';
        new Uri($input);
    }
}
