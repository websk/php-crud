<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDHtml;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterLike
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterLike implements InterfaceCRUDTableFilterVisible
{
    protected string $title;

    protected string $field_name;

    protected string $filter_uniq_id;

    protected string $comment_str = '';

    /**
     * CRUDTableFilterLike constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param string $comment_str = ''
     */
    public function __construct(string $filter_uniq_id, string $title, string $field_name, string $comment_str = '')
    {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setCommentStr($comment_str);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(ServerRequestInterface $request): array
    {
        $where = '';
        $placeholder_values_arr = [];

        $value = $request->getParam($this->getFilterUniqId(), '');

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($value != '') {
            // https://stackoverflow.com/questions/14926386/how-to-search-for-slash-in-mysql-and-why-escaping-not-required-for-wher
            // https://stackoverflow.com/questions/12041589/like-query-with-php-mysql-and-pdo
            $value = str_replace('\\', '\\\\', $value);

            $where .= ' ' . $column_name . ' like ? ';
            $placeholder_values_arr[] = '%' . $value . '%';
        }

        return [$where, $placeholder_values_arr];
    }

    /** @inheritdoc */
    public function getHtml(ServerRequestInterface $request): string
    {
        $html = CRUDHtml::tag('input', [
            'name' => $this->getFilterUniqId(),
            'value' => $request->getParam($this->getFilterUniqId(), ''),
            'class' => 'form-control',
            'onkeyup' => '$(this).closest(\'form\').submit();'
        ], '');

        if ($this->getCommentStr()) {
            $html .= '<span class="help-block">' . $this->getCommentStr() . '</span>';
        }

        return $html;
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

    /**
     * @return string
     */
    public function getCommentStr(): string
    {
        return $this->comment_str;
    }

    /**
     * @param string $comment_str
     */
    public function setCommentStr(string $comment_str): void
    {
        $this->comment_str = $comment_str;
    }
}
