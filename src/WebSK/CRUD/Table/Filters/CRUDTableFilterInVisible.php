<?php

namespace WebSK\CRUD\Table\Filters;

use OLOG\HTML;
use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterInVisible
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterInVisible implements InterfaceCRUDTableFilterVisible
{
    protected string $title;

    protected string $field_name;

    protected string $filter_uniq_id;

    protected bool $is_inline = false;

    protected string $placeholder = '';

    const DEFAULT_SEPARATOR = ';';
    protected string $separator = self::DEFAULT_SEPARATOR;

    /**
     * CRUDTableFilterIn constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param bool $is_inline
     * @param string $placeholder
     * @param string $separator
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        string $field_name,
        bool $is_inline = false,
        string $placeholder = '',
        string $separator = self::DEFAULT_SEPARATOR
    ) {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setInline($is_inline);
        $this->setPlaceholder($placeholder);
        $this->setSeparator($separator);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $placeholder_values_arr = [];
        $where = '';

        $value = $request->getParam($this->getFilterUniqId(), '');

        if ($value != '') {
            if (!is_array($value)) {
                $value = explode($this->getSeparator(), $value);
            }

            if(count($value) > 0) {
                $column_name = $this->getFieldName();
                $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

                $in_arr = [];
                foreach ($value as $val) {
                    $in_arr[] = '?';
                    $placeholder_values_arr[] = $val;
                }
                $where = $column_name . " IN (" . implode(',', $in_arr) . ")";
            }
        }

        return [$where, $placeholder_values_arr];
    }

    /** @inheritdoc */
    public function getHtml(Request $request): string
    {
        $html = '';
        $html .= HTML::tag('input', [
            'placeholder' => $this->getPlaceholder(),
            'name' => $this->getFilterUniqId(),
            'id' => $this->getFilterUniqId(),
            'class' => $this->isInline() ? '' : 'form-control',
            'value' => $request->getParam($this->getFilterUniqId(), '')
        ], '');

        ob_start();
        ?>
        <script>
            var CRUDTableFilterInVisible = function (elem_id) {
                var $input = $('#' + elem_id);
                var timer;
                var value = $input.val();

                $input.on('keyup paste', function (e) {
                    var $this = $(this);

                    if ((value == $this.val()) && (e.type != 'paste')) {
                        return;
                    }

                    value = $this.val();

                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        $this.closest('form').trigger('submit');
                    }, 200);
                });
            };
            new CRUDTableFilterInVisible('<?= $this->getFilterUniqId() ?>');
        </script>
        <?php
        $html .= ob_get_clean();

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
     * @param bool $is_inline
     */
    public function setInline(bool $is_inline): void
    {
        $this->is_inline = $is_inline;
    }

    /**
     * @return bool
     */
    public function isInline(): bool
    {
        return $this->is_inline;
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
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }
}
