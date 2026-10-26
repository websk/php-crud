<?php

namespace WebSK\CRUD\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\CRUDFormVerticalRow;
use WebSK\CRUD\Form\InterfaceCRUDFormWidget;
use WebSK\Entity\InterfaceEntity;

final class FormAccessibilityTest extends TestCase
{
    public function testHorizontalRowAssociatesItsTitleWithTheFirstVisibleControl(): void
    {
        $html = (new CRUDFormRow('Название', $this->widget()))->html($this->entity(), $this->crud());

        self::assertMatchesRegularExpression(
            '/<div id="(CRUDFormRowLabel_[^"]+)"[^>]*>Название<\/div>.*'
            . '<input type="hidden" name="token">.*<input name="name" aria-labelledby="\1">/s',
            $html
        );
    }

    public function testVerticalRowAssociatesItsTitleWithTheFirstVisibleControl(): void
    {
        $html = (new CRUDFormVerticalRow('Описание', $this->widget()))->html($this->entity(), $this->crud());

        self::assertMatchesRegularExpression(
            '/<div id="(CRUDFormRowLabel_[^"]+)"[^>]*>Описание<\/div>.*'
            . '<input type="hidden" name="token">.*<input name="name" aria-labelledby="\1">/s',
            $html
        );
    }

    private function widget(): InterfaceCRUDFormWidget
    {
        return new class implements InterfaceCRUDFormWidget {
            public function html(InterfaceEntity $entity_obj, CRUD $crud): string
            {
                return '<input type="hidden" name="token"><input name="name">';
            }
        };
    }

    private function entity(): InterfaceEntity
    {
        return new class implements InterfaceEntity {
            public function getId(): ?int
            {
                return null;
            }
        };
    }

    private function crud(): CRUD
    {
        return new CRUD(new class implements ContainerInterface {
            public function get(string $id): never
            {
                throw new \RuntimeException('Unexpected container lookup: ' . $id);
            }

            public function has(string $id): bool
            {
                return false;
            }
        });
    }
}
