<?php

namespace WebSK\CRUD\Table\Filters;

use OLOG\HTML;
use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterEqualInline
 * @package WebSK\CRUD
 */
class CRUDTableFilterEqualInline implements InterfaceCRUDTableFilterVisible
{
    const DEFAULT_TIMEOUT = 500;

    /** @var string */
    protected $title;

    /** @var string */
    protected $field_name;

    /** @var string */
    protected $filter_uniq_id;

    /** @var string */
    protected $placeholder;

    /** @var int */
    protected $timeout;

    /**
     * CRUDTableFilterEqualInline constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param string $placeholder
     * @param int $timeout
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        string $field_name,
        string $placeholder = '',
        int $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setPlaceholder($placeholder);
        $this->setTimeout($timeout);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $where = '';
        $placeholder_values_arr = [];

        // для этого виджета галка включения не выводится: если в поле пустая строка - он игрорируется
        $value = $request->getParam($this->getFilterUniqId(), '');

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($value != '') {
            $where .= ' ' . $column_name . ' = ? ';
            $placeholder_values_arr[] = $value;
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
            'value' => $request->getParam($this->getFilterUniqId(), ''),
            'class' => 'form-control'
        ], '');

        ob_start();
        ?>
        <script>
            var CRUDTableFilterEqualInline = function (elem_id) {
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
                    }, <?php echo $this->getTimeout(); ?>);
                });
            };
            new CRUDTableFilterEqualInline('<?php echo $this->getFilterUniqId(); ?>');
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
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }
}
