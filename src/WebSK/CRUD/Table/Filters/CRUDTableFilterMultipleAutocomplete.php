<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\CRUDTableJSON;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;
use WebSK\Utils\Sanitize;

/**
 * Class CRUDTableFilterMultipleAutocomplete
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterMultipleAutocomplete implements InterfaceCRUDTableFilterVisible
{
    const WIDGET_INPUT_ID_PREFIX = 'WidgetInput_';

    const REMOTE_SOURCE_MIN_SEARCH_LENGTH = 2;

    const REMOTE_SOURCE_DROPDOWN_MAX_ITEMS = 30;

    const DEFAULT_CONFIG = [
        'tokenize' => [
            'debounce' => 200,
            'searchMinLength' => self::REMOTE_SOURCE_MIN_SEARCH_LENGTH,
            'dropdownMaxItems' => self::REMOTE_SOURCE_DROPDOWN_MAX_ITEMS,
            'displayNoResultsMessage' => true,
            'noResultsMessageText' => 'Не удалось найти %s',
            'placeholder' => 'Начните вводить первые буквы названия'
        ]
    ];

    protected string $title;

    protected string $field_name;

    protected string $filter_uniq_id;

    protected string $reference_fitler_value;

    protected string $reference_fitler_text;

    protected string $remote_data_source_url;

    /**
     * CRUDTableFilterIn constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param string $reference_filter_value
     * @param string $reference_filter_text
     * @param string $remote_data_source_url
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        string $field_name,
        string $reference_filter_value,
        string $reference_filter_text,
        string $remote_data_source_url
    ) {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setReferenceFilterValue($reference_filter_value);
        $this->setReferenceFilterText($reference_filter_text);
        $this->setRemoteDataSourceUrl($remote_data_source_url);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        $placeholder_values_arr = [];
        $where = '';

        $value = $request->getParam($this->getFilterUniqId(), '');

        if ($value != '') {
            if (count($value) > 0) {
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

        $uniqid = uniqid(self::WIDGET_INPUT_ID_PREFIX . $this->getFieldName());
        $input_id = $uniqid . '_input';

        $html .= '<select multiple id="' . $input_id . '" name="' . Sanitize::sanitizeAttrValue($this->getFieldName()) . '[]"></select>';

        static $crud_table_filter_multiple_autocomplete_include_script;

        if (!isset($crud_table_filter_multiple_autocomplete_include_script)) {
            $html .= $this->getJavascript();
            $crud_table_filter_multiple_autocomplete_include_script = false;
        }

        $config = self::DEFAULT_CONFIG;
        $config['input_id'] = $input_id;
        $config['ajax_url'] = $this->getRemoteDataSourceUrl();
        $config['field_value_name'] = $this->getReferenceFilterValue();
        if ($request->getParam($this->getFieldName())) {
            $config['field_value_val'] = implode(
                CRUDTableFilterInVisible::DEFAULT_SEPARATOR,
                $request->getParam($this->getFieldName())
            );
        }
        $config['field_text_name'] = $this->getReferenceFilterText();
        $config['submit_form'] = true;

        $html .= '<script>createMultipleAutocompleteFilter(' . json_encode($config) . ');</script>';

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
    public function getReferenceFilterValue(): string
    {
        return $this->reference_fitler_value;
    }

    /**
     * @param string $reference_filter_value
     */
    public function setReferenceFilterValue(string $reference_filter_value): void
    {
        $this->reference_fitler_value = $reference_filter_value;
    }

    /**
     * @return string
     */
    public function getReferenceFilterText(): string
    {
        return $this->reference_fitler_text;
    }

    /**
     * @param string $reference_filter_text
     */
    public function setReferenceFilterText(string $reference_filter_text): void
    {
        $this->reference_fitler_text = $reference_filter_text;
    }

    /**
     * @return string
     */
    public function getRemoteDataSourceUrl(): string
    {
        return $this->remote_data_source_url;
    }

    /**
     * @param string $remote_data_source_url
     */
    public function setRemoteDataSourceUrl(string $remote_data_source_url): void
    {
        $this->remote_data_source_url = $remote_data_source_url;
    }

    /**
     * @return string
     */
    protected function getJavascript() : string
    {
        ob_start();
        ?>
        <script src="/assets/libraries/tokenize2/tokenize2.min.js"></script>
        <link href="/assets/libraries/tokenize2/tokenize2.min.css" rel="stylesheet" />
        <script type="text/javascript">
            function createMultipleAutocompleteFilter(config) {
                config = Object.assign({
                    input_id: '',
                    options: [],
                    submit_form: false,
                    field_value_name: '',
                    field_value_val: '',
                    field_text_name: '',
                    ajax_url: '',
                    tokenize: {}
                }, config);

                if (!config.input_id) {
                    return;
                }

                if (config.field_value_val && config.field_value_name && config.ajax_url) {
                    let ajax_url = new URL(config.ajax_url, document.location.origin);
                    ajax_url.searchParams.append(config.field_value_name, config.field_value_val);
                    $.ajax(ajax_url.href, {
                        dataType: 'json',
                        success: function (data) {
                            $.each(data, function (index, item) {
                                let item_data = [
                                    item[config.field_value_name],
                                    item[config.field_text_name],
                                    true
                                ];
                                $('#' + config.input_id).tokenize2().trigger('tokenize:tokens:add', item_data);
                                if (config.submit_form) {
                                    $('#' + config.input_id).on('tokenize:tokens:add tokenize:tokens:remove', function (container) {
                                        $(this).closest('form').trigger('submit');
                                    });
                                }
                            });
                        }
                    });
                }

                if (config.ajax_url) {
                    config.tokenize.dataSource = function (term, object) {
                        let ajax_url = new URL(config.ajax_url, document.location.origin);
                        ajax_url.searchParams.append('term', term);
                        if (config.field_text_name) {
                            ajax_url.searchParams.append(config.field_text_name, term);
                        }
                        $.ajax(ajax_url.href, {
                            dataType: 'json',
                            success: function (data) {
                                var $items = [];
                                $.each(data, function (index, item) {
                                    if (item.hasOwnProperty('value') && item.hasOwnProperty('text')) {
                                        $items.push(item);
                                    } else {
                                        let item_data = {
                                            'value': item[config.field_value_name],
                                            'text': item[config.field_text_name],
                                        };
                                        $items.push(item_data);
                                    }
                                });
                                object.trigger('tokenize:dropdown:fill', [$items]);
                            }
                        });

                        if (config.submit_form) {
                            $('#' + config.input_id).on('tokenize:tokens:add tokenize:tokens:remove', function (container) {
                                $(this).closest('form').trigger('submit');
                            });
                        }
                    };
                }

                $('#' + config.input_id).tokenize2(config.tokenize);

                for (let option in config.options) {
                    $('#' + config.input_id).tokenize2().trigger('tokenize:tokens:add', config.options[option]);
                }

                if (!config.ajax_url) {
                    $(document).ready(function () {
                        $('#' + config.input_id).on('tokenize:select', function (container) {
                            $(this).tokenize2().trigger('tokenize:search', [$(this).tokenize2().input.val()]);
                        });
                    });
                }
            }
        </script>
        <?php
        $html = ob_get_clean();

        return $html;
    }
}