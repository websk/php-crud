<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDHtml;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterEqualIntervalInline
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterEqualIntervalInline implements InterfaceCRUDTableFilterVisible
{
    protected string $title;

    protected string $field_name;

    protected string $filter_start_uniq_id;

    protected string $filter_end_uniq_id;

    protected string $placeholder;

    /**
     * CRUDTableFilterEqualIntervalInline constructor.
     * @param string $filter_start_uniq_id
     * @param string $filter_end_uniq_id
     * @param string $title
     * @param string $field_name
     * @param string $placeholder
     */
    public function __construct(
        string $filter_start_uniq_id,
        string $filter_end_uniq_id,
        string $title,
        string $field_name,
        string $placeholder = ''
    ) {
        $this->setFilterStartUniqId($filter_start_uniq_id);
        $this->setFilterEndUniqId($filter_end_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setPlaceholder($placeholder);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(ServerRequestInterface $request): array
    {
        $where = '';
        $placeholder_values_arr = [];

        // для этого виджета галка включения не выводится: если в поле пустая строка - он игрорируется
        $value_start = $request->getParam($this->getFilterStartUniqId(), '');
        $value_end = $request->getParam($this->getFilterEndUniqId(), '');

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($value_start != '') {
            $where .= ' ' . $column_name . ' >= ? ';
            $placeholder_values_arr[] = $value_start;
        }

        if ($value_end != '') {
            if ($value_start != '') {
                $where .= ' AND ';
            }
            $where .= ' ' . $column_name . ' <= ? ';
            $placeholder_values_arr[] = $value_end;
        }

        return [$where, $placeholder_values_arr];
    }

    /** @inheritdoc */
    public function getHtml(ServerRequestInterface $request): string
    {
        $html = 'с ';
        $html .= CRUDHtml::tag('input', [
            'placeholder' => $this->getPlaceholder(),
            'name' => $this->getFilterStartUniqId(),
            'id' => $this->getFilterStartUniqId(),
            'value' => $request->getParam($this->getFilterStartUniqId(), '')
        ], '');

        $html .= ' по ';
        $html .= CRUDHtml::tag('input', [
            'placeholder' => $this->getPlaceholder(),
            'name' => $this->getFilterEndUniqId(),
            'id' => $this->getFilterEndUniqId(),
            'value' => $request->getParam($this->getFilterEndUniqId(), '')
        ], '');

        ob_start();
        ?>
        <script>
            var CRUDTableFilterEqualIntervalInline = function (elem_id) {
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
                    }, 1000);
                });
            };
            new CRUDTableFilterEqualIntervalInline('<?= $this->getFilterStartUniqId() ?>');
            new CRUDTableFilterEqualIntervalInline('<?= $this->getFilterEndUniqId() ?>');
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
     * @return string
     */
    public function getFilterStartUniqId(): string
    {
        return $this->filter_start_uniq_id;
    }

    /**
     * @param string $filter_start_uniq_id
     */
    public function setFilterStartUniqId(string $filter_start_uniq_id): void
    {
        $this->filter_start_uniq_id = $filter_start_uniq_id;
    }

    /**
     * @return string
     */
    public function getFilterEndUniqId(): string
    {
        return $this->filter_end_uniq_id;
    }

    /**
     * @param string $filter_end_uniq_id
     */
    public function setFilterEndUniqId(string $filter_end_uniq_id): void
    {
        $this->filter_end_uniq_id = $filter_end_uniq_id;
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
}
