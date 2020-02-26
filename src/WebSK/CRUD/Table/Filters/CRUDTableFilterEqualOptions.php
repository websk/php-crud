<?php

namespace WebSK\CRUD\Table\Filters;

use WebSK\Utils\Sanitize;
use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterEqualOptions
 * @package WebSK\CRUD
 */
class CRUDTableFilterEqualOptions implements InterfaceCRUDTableFilterVisible
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $field_name;

    /** @var string */
    protected $filter_iniq_id;

    /** @var bool */
    protected $initial_is_enabled;

    /** @var string */
    protected $initial_value;

    /** @var array */
    protected $options_arr;

    /** @var bool */
    protected $show_null_checkbox;

    /**
     * CRUDTableFilterEqualOptions constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param array $options_arr
     * @param bool $initial_is_enabled
     * @param string $initial_value
     * @param bool $show_null_checkbox
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        string $field_name,
        array $options_arr,
        bool $initial_is_enabled,
        string $initial_value,
        bool $show_null_checkbox
    ) {
        $this->setFilterIniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setInitialIsEnabled($initial_is_enabled);
        $this->setInitialValue($initial_value);
        $this->setShowNullCheckbox($show_null_checkbox);
    }

    /**
     * сообщает, нужно ли использовать значения из формы
     * (включая отсутствующие в форме поля - для чекбоксов это означает false)
     * или этот фильтр в форме не приходил и нужно использовать initial значения
     * @param Request $request
     * @return bool
     */
    public function useValuesFromForm(Request $request): bool
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
        return Sanitize::sanitizeAttrValue($this->getFilterIniqId() . '___is_null');
    }

    /**
     * @param Request $request
     * @return null|string
     */
    public function getValue(Request $request): ?string
    {
        if (!$this->useValuesFromForm($request)) {
            return $this->getInitialValue();
        }

        $value = $request->getParam($this->getFilterIniqId(), '');
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
        return Sanitize::sanitizeAttrValue($this->getFilterIniqId() . '___enabled');
    }

    /**
     * @param string $field_value
     * @param string $input_name
     * @return string
     */
    public function widgetHtmlForValue(string $field_value, string $input_name): string
    {
        $html = '';

        $html .= '<select onchange="f' . $input_name . '_selectchange(this);" id="' .
            $input_name . '" name="' . $input_name . '" class="form-control">';

        $options_arr = $this->getOptionsArr();
        foreach ($options_arr as $value => $title) {
            $html .= '<option value="' . $value . '" ' . ($field_value == $value ? 'selected' : '') . '>' .
                $title . '</option>';
        }

        $html .= '</select>';

        if ($this->isShowNullCheckbox()) {
            $html .= '<div class="input-group-addon">';

            $html .= '<input type="checkbox" onchange="f' . $input_name . '_nullchange(this);" '.
                'value="1" id="' . $this->nullCheckboxInputName() . '" name="' . $this->nullCheckboxInputName() . '" ' .
                (is_null($field_value) ? ' checked ' : '') . ' /> null';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @return string
     */
    public function filterIsPassedInputName(): string
    {
        return $this->getFilterIniqId() . '___passed';
    }

    /** @inheritdoc */
    public function getHtml(Request $request): string
    {
        $html = '';

        $input_name = $this->getFilterIniqId();

        $html .= '<div class="input-group">';

        // отдельное поле, наличие которого сообщает что фильтр присутствует в форме
        // (все другие поля могут отсутствовать когда фильтр например запрещен и т.п.)
        $html .= '<input type="hidden" name="' . $this->filterIsPassedInputName() . '" value="1">';

        $html .= $this->widgetHtmlForValue($this->getValue($request), $input_name);

        $html .= '<div class="input-group-addon">';
        $html .= '<label>';
        $html .= '<input title="Filter active" onchange="f' . $input_name . '_enabledclick(this);" '.
            'type="checkbox" id="' . $this->enabledCheckboxInputName() . '" '.
            'name="' . $this->enabledCheckboxInputName() . '" ' .
            ($this->isEnabled($request) ? 'checked' : '') . ' value="1">';
        $html .= '</label>';
        $html .= '</div>';

        $html .= '</div>';

        $html .= '<script>

        function f' . $input_name . '_selectchange(select_element){
            $(select_element).closest("form").submit();
        }
        
        function f' . $input_name . '_nullchange(checkbox_element){
            f' . $input_name . '_updatedisabled();
            $(checkbox_element).closest("form").submit();
        }
        
        function f' . $input_name . '_enabledclick(checkbox_element){
            f' . $input_name . '_updatedisabled();
            $(checkbox_element).closest("form").submit();
        }
        
        function f' . $input_name . '_updatedisabled(){
            var enabled = $("#' . $this->enabledCheckboxInputName() . '").prop("checked");
            if (enabled){
                $("#' . $this->nullCheckboxInputName() . '").prop("disabled", false);
                if ($("#' . $this->nullCheckboxInputName() . '").length > 0){ // if widget has null checkbox
                    var is_null = $("#' . $this->nullCheckboxInputName() . '").prop("checked");
                    $("#' . $input_name . '").prop("disabled", is_null);
                } else {
                    $("#' . $input_name . '").prop("disabled", false);
                }
            } else {
                $("#' . $input_name . '").prop("disabled", true);
                $("#' . $this->nullCheckboxInputName() . '").prop("disabled", true);
            }
        }

        f' . $input_name . '_updatedisabled();
        
        </script>';

        return $html;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isEnabled(Request $request): bool
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
    public function sqlConditionAndPlaceholderValue(Request $request): array
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
    public function getFilterIniqId(): string
    {
        return $this->filter_iniq_id;
    }

    /**
     * @param string $filter_iniq_id
     */
    public function setFilterIniqId(string $filter_iniq_id): void
    {
        $this->filter_iniq_id = $filter_iniq_id;
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
}
