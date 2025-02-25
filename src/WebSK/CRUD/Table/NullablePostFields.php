<?php

namespace WebSK\CRUD\Table;

use Psr\Http\Message\ServerRequestInterface;
use WebSK\Slim\Request;
use WebSK\Utils\Sanitize;

/**
 * Class NullablePostFields
 * @package WebSK\CRUD
 */
class NullablePostFields
{
    /**
     * @param string $field_name
     * @param string $field_value
     * @return string
     */
    public static function hiddenFieldHtml(string $field_name, string $field_value): string
    {
        $is_null_value = '';
        if (is_null($field_value)) {
            $is_null_value = '1';
        }

        $html = '';
        $html .= '<input type="hidden" name="' . Sanitize::sanitizeAttrValue($field_name) . '" ' .
            'value="' . Sanitize::sanitizeAttrValue($field_value) . '"/>';
        $html .= '<input type="hidden" name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null" ' .
            'value="' . Sanitize::sanitizeAttrValue($is_null_value) . '"/>';

        return $html;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $field_name
     * @return null|string
     */
    public static function optionalFieldValue(ServerRequestInterface $request, string $field_name): ?string
    {
        $field_value = Request::getParsedBodyParam($request, $field_name, '');

        // чтение возможных NULL
        if (Request::getParsedBodyParam($request, $field_name . "___is_null", '') == "1") {
            $field_value = null;
        }

        return $field_value;
    }
}
