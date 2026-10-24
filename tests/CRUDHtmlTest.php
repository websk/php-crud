<?php

namespace WebSK\CRUD\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\CRUD\CRUDHtml;

final class CRUDHtmlTest extends TestCase
{
    public function testVoidElementDoesNotReceiveClosingTag(): void
    {
        $html = CRUDHtml::tag('input', [
            'type' => 'text',
            'name' => 'query',
        ]);

        self::assertSame('<input type="text" name="query">', $html);
    }

    public function testEmptyOptionalDivAttributesAreOmitted(): void
    {
        self::assertSame('<div>content</div>', CRUDHtml::div('', '', 'content'));
    }

    public function testRegularElementIsClosedAndAttributesAreEscaped(): void
    {
        self::assertSame(
            '<a href="/search?q=&quot;value&quot;">link</a>',
            CRUDHtml::tag('a', ['href' => '/search?q="value"'], 'link'),
        );
    }
}
