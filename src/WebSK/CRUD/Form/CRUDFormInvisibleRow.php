<?php

namespace WebSK\CRUD\Form;

use OLOG\HTML;
use WebSK\CRUD\CRUD;

/**
 * Class CRUDFormInvisibleRow
 * @package WebSK\CRUD
 */
class CRUDFormInvisibleRow implements InterfaceCRUDFormRow
{
    /** @var InterfaceCRUDFormWidget */
    protected $widget_obj;

    /**
     * CRUDFormInvisibleRow constructor.
     * @param InterfaceCRUDFormWidget $widget_obj
     */
    public function __construct(InterfaceCRUDFormWidget $widget_obj)
    {
        $this->setWidgetObj($widget_obj);
    }

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        return HTML::tag('div', ['style' => 'display: none;'], $this->getWidgetObj()->html($obj, $crud));
    }

    /**
     * @return InterfaceCRUDFormWidget
     */
    public function getWidgetObj(): InterfaceCRUDFormWidget
    {
        return $this->widget_obj;
    }

    /**
     * @param InterfaceCRUDFormWidget $widget_obj
     */
    public function setWidgetObj(InterfaceCRUDFormWidget $widget_obj): void
    {
        $this->widget_obj = $widget_obj;
    }
}
