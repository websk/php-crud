<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDHtml;
use WebSK\Utils\Sanitize;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterEqualOptionsInline
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterEqualOptionsInline implements InterfaceCRUDTableFilterVisible
{
    const string IS_NULL_POSTFIX = '___null';
    const string ENABLED_POSTFIX = '___enabled';
    const string PASSED_POSTFIX = '___passed';

    protected string $title;

    protected string $field_name;

    protected string $filter_uniq_id;

    protected bool $initial_is_enabled = false;

    protected string $initial_value = '';

    protected array $options_arr;

    protected bool $show_null_checkbox = false;

    protected string $btn_all_text = 'Все';

    /**
     * CRUDTableFilterEqualOptionsInlineVisible constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param array $options_arr
     * @param bool $initial_is_enabled
     * @param string $initial_value
     * @param bool $show_null_checkbox
     * @param string $btn_all_text
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        string $field_name,
        array $options_arr,
        bool $initial_is_enabled = false,
        string $initial_value = '',
        bool $show_null_checkbox = false,
        string $btn_all_text = 'Все'
    ) {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setInitialIsEnabled($initial_is_enabled);
        $this->setInitialValue($initial_value);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setBtnAllText($btn_all_text);
    }

    /**
     * сообщает, нужно ли использовать значения из формы (включая отсутствующие в форме поля -
     * для чекбоксов это означает false) или этот фильтр в форме не приходил и нужно использовать initial значения
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function useValuesFromForm(ServerRequestInterface $request): bool
    {
        $value = $request->getParam($this->filterIsPassedInputName(), null);

        if (is_null($value)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function nullCheckboxInputName(): string
    {
        return Sanitize::sanitizeAttrValue($this->getFilterUniqId() . self::IS_NULL_POSTFIX);
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|string
     */
    public function getValue(ServerRequestInterface $request): ?string
    {
        if (!$this->useValuesFromForm($request)) {
            return $this->getInitialValue();
        }

        $value = $request->getParam($this->getFilterUniqId(), '');
        $is_null = $request->getParam($this->nullCheckboxInputName(), '');

        if ($is_null != '') {
            $value = null;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function enabledCheckboxInputName(): string
    {
        return Sanitize::sanitizeAttrValue($this->getFilterUniqId() . self::ENABLED_POSTFIX);
    }

    /**
     * @return string
     */
    public function filterIsPassedInputName(): string
    {
        return $this->getFilterUniqId() . self::PASSED_POSTFIX;
    }

    /** @inheritdoc */
    public function getHtml(ServerRequestInterface $request): string
    {
        $html = CRUDHtml::div('js-filter', '', function () use ($request) {
            $input_name = $this->getFilterUniqId();
            /**
             * отдельное поле, наличие которого сообщает что фильтр присутствует в форме
             * (все другие поля могут отсутствовать когда фильтр например запрещен и т.п.)
             */
            echo '<input type="hidden" name="' . $this->filterIsPassedInputName() . '" value="1">';

            echo '<input type="hidden" name="' . $this->enabledCheckboxInputName() . '" ' .
                'value="' . ($this->isEnabled($request) ? '1' : '') . '">';
            echo '<span onclick="f' . $input_name . '_changeFiltres(this);" class="btn btn-xs btn-default ' .
                ($this->isEnabled($request) ? '' : 'active') . '">' . $this->getBtnAllText() . '</span>';

            echo '<input type="hidden" name="' . $input_name . '" ' .
                'value="' . ($this->isEnabled($request) ? $this->getValue($request) : '') . '">';
            $options_arr = $this->getOptionsArr();
            foreach ($options_arr as $value => $title) {
                echo '<span data-value="' . $value . '" data-enabled="1" ' .
                    'onclick="f' . $input_name . '_changeFiltres(this);" class="btn btn-xs btn-default ' .
                    (($this->isEnabled($request) && ($this->getValue($request) == $value)) ? 'active' : '') .
                    '">' . $title . '</span>';
            }

            if ($this->isShowNullCheckbox()) {
                echo '<input type="hidden" name="' . $this->nullCheckboxInputName() . '" ' .
                    'value="' .
                    ((is_null($this->getValue($request)) && ($this->isEnabled($request))) ? '1' : '') . '">';
                echo '<span data-isnull="1" data-enabled="1" onclick="f' . $input_name . '_changeFiltres(this);" ' .
                    'class="btn btn-xs btn-default ' .
                    ((is_null($this->getValue($request)) && ($this->isEnabled($request))) ? 'active' : '') . '">'.
                    'Не указано</span>';
            }
        });

        $input_name = $this->getFilterUniqId();
        ob_start(); ?>
        <script>
            function f<?= $input_name ?>_changeFiltres(select_element) {
                var $this = $(select_element);
                var $form = $this.closest('form');
                var $filter = $this.closest('.js-filter');

                $filter.find('.btn').removeClass('active');
                $this.addClass('active');

                var enabled = $this.data('enabled') || '';
                var value = $this.data('value') || '';
                var isnull = $this.data('isnull') || '';

                $filter.find('[name="<?= $this->enabledCheckboxInputName() ?>"]').val(enabled);
                $filter.find('[name="<?= $input_name ?>"]').val(value);
                $filter.find('[name="<?= $this->nullCheckboxInputName() ?>"]').val(isnull);

                $form.submit();
            }
        </script>
        <?php
        $script = ob_get_clean();

        return $html . $script;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isEnabled(ServerRequestInterface $request): bool
    {
        if (!$this->useValuesFromForm($request)) {
            return $this->isInitialIsEnabled();
        }

        $is_enabled_from_form = $request->getParam($this->enabledCheckboxInputName(), '');

        if ($is_enabled_from_form != '') {
            return true;
        }

        return false;
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(ServerRequestInterface $request): array
    {
        if (!$this->isEnabled($request)) {
            return ['', []];
        }

        $value = $this->getValue($request);
        $sanitized_column_name = Sanitize::sanitizeSqlColumnName($this->getFieldName());

        if (is_null($value)) {
            return [' ' . $sanitized_column_name . ' is null ', []];
        }

        return [' ' . $sanitized_column_name . ' = ? ', [$value]];
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
     * @return bool
     */
    public function isInitialIsEnabled(): bool
    {
        return $this->initial_is_enabled;
    }

    /**
     * @param bool $initial_is_enabled
     */
    public function setInitialIsEnabled(bool $initial_is_enabled): void
    {
        $this->initial_is_enabled = $initial_is_enabled;
    }

    /**
     * @return string
     */
    public function getInitialValue(): string
    {
        return $this->initial_value;
    }

    /**
     * @param string $initial_value
     */
    public function setInitialValue(string $initial_value): void
    {
        $this->initial_value = $initial_value;
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

    /**
     * @return bool
     */
    public function isShowNullCheckbox(): bool
    {
        return $this->show_null_checkbox;
    }

    /**
     * @param bool $show_null_checkbox
     */
    public function setShowNullCheckbox(bool $show_null_checkbox): void
    {
        $this->show_null_checkbox = $show_null_checkbox;
    }

    /**
     * @return string
     */
    public function getBtnAllText(): string
    {
        return $this->btn_all_text;
    }

    /**
     * @param string $btn_all_text
     */
    public function setBtnAllText(string $btn_all_text): void
    {
        $this->btn_all_text = $btn_all_text;
    }
}
