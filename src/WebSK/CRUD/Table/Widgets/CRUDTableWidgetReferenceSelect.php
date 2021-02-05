<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\Utils\Assert;
use WebSK\Utils\Sanitize;
use WebSK\Entity\InterfaceEntity;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDFieldsAccess;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetReferenceSelect
 * @package WebSK\CRUD
 */
class CRUDTableWidgetReferenceSelect implements InterfaceCRUDTableWidget
{
    protected string $title_field_name;

    protected string $id_field_name;

    /**
     * CRUDTableWidgetReferenceSelect constructor.
     * @param string $title_field_name
     * @param string $id_field_name
     */
    public function __construct(string $title_field_name, string $id_field_name = '')
    {
        $this->setTitleFieldName($title_field_name);
        $this->setIdFieldName($id_field_name);
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @param CRUD $crud
     * @return string
     * @throws \ReflectionException
     */
    public function html(InterfaceEntity $entity_obj, CRUD $crud): string
    {
        Assert::assert($entity_obj);

        $title_field_name = $this->getTitleFieldName();

        $obj_title = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $title_field_name);

        $id_field_name = $this->getIdFieldName();
        if ($id_field_name == '') {
            $id = CRUDFieldsAccess::getObjId($entity_obj);
        } else {
            $id = CRUDFieldsAccess::getObjectFieldValue($entity_obj, $id_field_name);
        }

        return '<button class="btn btn-xs btn-default js-ajax-form-select" type="submit" ' .
            'data-id="' . Sanitize::sanitizeAttrValue($id) . '" ' .
            'data-title="' . Sanitize::sanitizeAttrValue($obj_title) . '">Выбор</button>';
    }

    /**
     * @return string
     */
    public function getTitleFieldName(): string
    {
        return $this->title_field_name;
    }

    /**
     * @param string $title_field_name
     */
    public function setTitleFieldName(string $title_field_name): void
    {
        $this->title_field_name = $title_field_name;
    }

    /**
     * @return string
     */
    public function getIdFieldName(): string
    {
        return $this->id_field_name;
    }

    /**
     * @param string $id_field_name
     */
    public function setIdFieldName(string $id_field_name): void
    {
        $this->id_field_name = $id_field_name;
    }
}
