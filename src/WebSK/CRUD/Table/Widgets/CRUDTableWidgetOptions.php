<?php

namespace WebSK\CRUD\Table\Widgets;

use Closure;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\CRUDCompiler;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableWidget;

/**
 * Class CRUDTableWidgetOptions
 * @package WebSK\CRUD
 */
class CRUDTableWidgetOptions implements InterfaceCRUDTableWidget
{
    /** @var string|Closure */
    protected $value;

    /** @var array */
    protected $options_arr;

    /** @inheritdoc */
    public function html($obj, CRUD $crud): string
    {
        $value = CRUDCompiler::fieldValueOrCallableResult($this->getValue(), $obj);

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
     * @return string|Closure
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|Closure $value
     */
    public function setValue($value): void
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
