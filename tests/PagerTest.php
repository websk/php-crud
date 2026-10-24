<?php

namespace WebSK\CRUD\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\CRUD\Pager;

final class PagerTest extends TestCase
{
    /** @return iterable<string, array{array<string, mixed>, int}> */
    public static function pageOffsetProvider(): iterable
    {
        yield 'missing' => [[], 0];
        yield 'positive' => [['table_users_page_offset' => '25'], 25];
        yield 'negative' => [['table_users_page_offset' => '-1'], 0];
    }

    /** @param array<string, mixed> $query */
    #[DataProvider('pageOffsetProvider')]
    public function testPageOffset(array $query, int $expected): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/')->withQueryParams($query);

        self::assertSame($expected, Pager::getPageOffset($request, 'users'));
    }

    /** @return iterable<string, array{array<string, mixed>, int}> */
    public static function pageSizeProvider(): iterable
    {
        yield 'missing' => [[], 100];
        yield 'valid' => [['table_users_page_size' => '50'], 50];
        yield 'zero' => [['table_users_page_size' => '0'], 100];
        yield 'too large' => [['table_users_page_size' => '1001'], 100];
    }

    /** @param array<string, mixed> $query */
    #[DataProvider('pageSizeProvider')]
    public function testPageSize(array $query, int $expected): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/')->withQueryParams($query);

        self::assertSame($expected, Pager::getPageSize($request, 'users'));
    }

    public function testPagerUsesBootstrapFiveMarkup(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');

        $html = Pager::renderPager($request, 'users', 100, true, 250);

        self::assertStringContainsString('class="pagination"', $html);
        self::assertStringContainsString('class="page-item active" aria-current="page"', $html);
        self::assertStringContainsString('class="page-link next-page"', $html);
        self::assertStringContainsString('class="page-item disabled"', $html);
        self::assertStringNotContainsString('<li class="active">', $html);
        self::assertStringNotContainsString('</a></a>', $html);
    }
}
