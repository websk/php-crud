<?php

namespace WebSK\CRUD\Table\Filters;

use OLOG\HTML;
use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterLike
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterLike implements InterfaceCRUDTableFilterVisible
{
    /** @var string */
    protected $title;
    /** @var string */
    protected $field_name;
    /** @var string */
    protected $filter_uniq_id;

    /**
     * CRUDTableFilterLike constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     */
    public function __construct(string $filter_uniq_id, string $title, string $field_name)
    {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $where = '';
        $placeholder_values_arr = [];

        $value = $request->getParam($this->getFilterUniqId(), '');

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($value != '') {
            $where .= ' ' . $column_name . ' like ? ';
            $placeholder_values_arr[] = '%' . $value . '%';
        }

        return [$where, $placeholder_values_arr];
    }

    /** @inheritdoc */
    public function getHtml(Request $request): string
    {
        return HTML::tag('input', [
            'name' => $this->getFilterUniqId(),
            'value' => $request->getParam($this->getFilterUniqId(), ''),
            'class' => 'form-control',
            'onkeyup' => '$(this).closest(\'form\').submit();'
        ], '');
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field_name;
    }

    /**
     * @param string $field_name
     */
    public function setFieldName(string $field_name): void
    {
        $this->field_name = $field_name;
    }

    /**
     * @return string
     */
    public function getFilterUniqId(): string
    {
        return $this->filter_uniq_id;
    }

    /**
     * @param string $filter_uniq_id
     */
    public function setFilterUniqId(string $filter_uniq_id): void
    {
        $this->filter_uniq_id = $filter_uniq_id;
    }
}
