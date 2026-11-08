<?php

namespace WebSK\CRUD\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;

final class FilterRequestInputTest extends TestCase
{
    public function testInlineLikeFilterReadsAjaxPostBody(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['block_title' => 'Комментарии']);
        $filter = new CRUDTableFilterLikeInline('block_title', 'Название', 'title');

        self::assertSame(
            [' title like ? ', ['%Комментарии%']],
            $filter->sqlConditionAndPlaceholderValue($request)
        );
        self::assertStringContainsString('value="Комментарии"', $filter->getHtml($request));
    }

    public function testInlineLikeFilterStillReadsInitialQueryString(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/')
            ->withQueryParams(['block_title' => 'Меню']);
        $filter = new CRUDTableFilterLikeInline('block_title', 'Название', 'title');

        self::assertSame(
            [' title like ? ', ['%Меню%']],
            $filter->sqlConditionAndPlaceholderValue($request)
        );
    }
}
