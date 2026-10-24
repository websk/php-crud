<?php

namespace WebSK\CRUD\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDateTime;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\Entity\InterfaceEntity;

final class NativeDateWidgetTest extends TestCase
{
    public function testDateWidgetUsesNativeControlAndSqlDateValue(): void
    {
        $entity = new NativeDateWidgetEntity(date: '2026-10-20 08:30:45');
        self::assertSame('2026-10-20 08:30:45', $entity->date());
        $html = (new CRUDFormWidgetDate('date'))->html($entity, $this->createStub(CRUD::class));

        self::assertStringContainsString('type="date"', $html);
        self::assertStringContainsString('name="date"', $html);
        self::assertStringContainsString('value="2026-10-20"', $html);
        self::assertStringNotContainsString('datetimepicker', $html);
    }

    public function testDateTimeWidgetPreservesSqlSubmissionFormat(): void
    {
        $entity = new NativeDateWidgetEntity(dateTime: '2026-10-20 08:30:45');
        self::assertSame('2026-10-20 08:30:45', $entity->dateTime());
        $html = (new CRUDFormWidgetDateTime('dateTime', false, true))->html(
            $entity,
            $this->createStub(CRUD::class),
        );

        self::assertStringContainsString('type="datetime-local"', $html);
        self::assertStringContainsString('value="2026-10-20T08:30:45"', $html);
        self::assertStringContainsString('value="2026-10-20 08:30:45"', $html);
        self::assertMatchesRegularExpression('/type="datetime-local"[^>]+required/', $html);
        self::assertDoesNotMatchRegularExpression('/type="hidden"[^>]+required/', $html);
        self::assertStringContainsString("replace('T', ' ')", $html);
        self::assertStringNotContainsString('moment', strtolower($html));
    }

    public function testTimestampWidgetPreservesUnixSubmissionFormat(): void
    {
        $timestamp = strtotime('2026-10-20 08:30:45');
        $entity = new NativeDateWidgetEntity(timestamp: $timestamp);
        self::assertSame($timestamp, $entity->timestamp());
        $html = (new CRUDFormWidgetTimestamp('timestamp', false, true))->html(
            $entity,
            $this->createStub(CRUD::class),
        );

        self::assertStringContainsString('type="datetime-local"', $html);
        self::assertStringContainsString('value="' . date('Y-m-d\TH:i:s', $timestamp) . '"', $html);
        self::assertStringContainsString('value="' . $timestamp . '"', $html);
        self::assertStringContainsString('Math.floor(new Date(value).getTime() / 1000)', $html);
        self::assertMatchesRegularExpression('/type="datetime-local"[^>]+required/', $html);
        self::assertDoesNotMatchRegularExpression('/type="hidden"[^>]+required/', $html);
    }

    public function testNullableWidgetClearsNativeAndSubmittedValues(): void
    {
        $entity = new NativeDateWidgetEntity(dateTime: null);
        self::assertNull($entity->dateTime());
        $html = (new CRUDFormWidgetDateTime('dateTime', true))->html(
            $entity,
            $this->createStub(CRUD::class),
        );

        self::assertStringContainsString('name="dateTime___is_null"', $html);
        self::assertStringContainsString(' checked ', $html);
        self::assertStringContainsString(".val('').trigger('change')", $html);
    }
}

final class NativeDateWidgetEntity implements InterfaceEntity
{
    public function __construct(
        private ?string $date = null,
        private ?string $dateTime = null,
        private ?int $timestamp = null,
    ) {
    }

    public function getId(): ?int
    {
        return null;
    }

    public function date(): ?string
    {
        return $this->date;
    }

    public function dateTime(): ?string
    {
        return $this->dateTime;
    }

    public function timestamp(): ?int
    {
        return $this->timestamp;
    }
}
