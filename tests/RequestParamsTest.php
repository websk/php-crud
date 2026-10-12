<?php

namespace WebSK\CRUD\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\CRUD\RequestParams;

final class RequestParamsTest extends TestCase
{
    public function testReadsQueryParameterAndDefault(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/')
            ->withQueryParams(['page' => '3']);

        self::assertSame('3', RequestParams::query($request, 'page'));
        self::assertSame(100, RequestParams::query($request, 'missing', 100));
    }

    public function testReadsArrayAndObjectParsedBodies(): void
    {
        $factory = new ServerRequestFactory();
        $array_request = $factory->createServerRequest('POST', '/')->withParsedBody(['name' => 'array']);
        $object_request = $factory->createServerRequest('POST', '/')->withParsedBody((object)['name' => 'object']);

        self::assertSame('array', RequestParams::parsedBody($array_request, 'name'));
        self::assertSame('object', RequestParams::parsedBody($object_request, 'name'));
        self::assertNull(RequestParams::parsedBody($object_request, 'missing'));
    }

    public function testNormalizesValidatedQueryValues(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/')
            ->withQueryParams([
                'search' => 42,
                'ids' => [1, '2'],
            ]);

        self::assertSame('42', RequestParams::queryString($request, 'search'));
        self::assertSame(['1', '2'], RequestParams::queryStringList($request, 'ids'));
    }

    public function testRejectsNestedQueryValues(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/')
            ->withQueryParams(['search' => ['nested']]);

        $this->expectException(\InvalidArgumentException::class);
        RequestParams::queryString($request, 'search');
    }

    public function testReadsIntegerFromParsedBody(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['id' => '17']);

        self::assertSame(17, RequestParams::parsedInt($request, 'id'));
    }
}
