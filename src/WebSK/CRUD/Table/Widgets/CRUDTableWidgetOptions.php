<?php

namespace WebSK\CRUD\Table\Widgets;

use WebSK\CRUD\CRUD;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetOptions
 * @package WebSK\CRUD
 */
class CRUDTableWidgetOptions implements InterfaceCRUDTableWidget
{
    /** @var string */
    protected $value;
    /** @var array */
    protected $options_arr;

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $value = $crud->compile($this->getValue(), ['this' => $obj]);

        $html = "UNDEFINED";
        $options_arr = $this->getOptionsArr();
        if (array_key_exists($value, $options_arr)) {
            $html = $options_arr[$value];
        }
        return Sanitize::sanitizeTagContent($html);
    }

    /**
     * CRUDTableWidgetOptions constructor.
     * @param string $value
     * @param array $options_arr
     */
    public function __construct(string $value, array $options_arr)
    {
        $this->setOptionsArr($options_arr);
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getOptionsArr(): array
    {
        return $this->options_arr;
    }

    /**
     * @param array $options_arr
     */
    public function setOptionsArr(array $options_arr): void
    {
        $this->options_arr = $options_arr;
    }
}
