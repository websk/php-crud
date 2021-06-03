<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;

/**
 * Class CRUDTableFilterMultipleAutocompleteInline
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterMultipleAutocompleteInline extends CRUDTableFilterMultipleAutocomplete
{
    protected static bool $css_is_loaded = false;

    /** @inheritdoc */
    public function getHtml(Request $request): string
    {
        $html = self::loadCSS();
        $html .= parent::getHtml($request);

        return $html;
    }

    /**
     * @return string
     */
    protected static function loadCSS() : string
    {
        if (self::$css_is_loaded) {
            return '';
        }

        self::$css_is_loaded = true;

        return
            '<style>
                ul.tokens-container {
                    min-width: 190px;
                    min-height: unset !important;
                    margin-top: 0 !important;
                    margin-bottom: 0 !important;           
                }
                ul.tokens-container li {
                    margin: 1px 0 0 0 !important;
                }
            </style>';
    }
}
