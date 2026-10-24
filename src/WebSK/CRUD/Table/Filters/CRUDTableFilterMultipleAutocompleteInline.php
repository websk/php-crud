<?php

namespace WebSK\CRUD\Table\Filters;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CRUDTableFilterMultipleAutocompleteInline
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterMultipleAutocompleteInline extends CRUDTableFilterMultipleAutocomplete
{
    protected static bool $css_is_loaded = false;

    /** @inheritdoc */
    public function getHtml(ServerRequestInterface $request): string
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

        return '<script>
            window.CRUDAssets = window.CRUDAssets || {};
            window.CRUDAssets.loadStyles = window.CRUDAssets.loadStyles || function () {
                if (document.querySelector("link[data-crud-styles]")) {
                    return;
                }
                var link = document.createElement("link");
                link.rel = "stylesheet";
                link.href = "/assets/libraries/crud/crud.css";
                link.dataset.crudStyles = "";
                document.head.appendChild(link);
            };
            window.CRUDAssets.loadStyles();
        </script>';
    }
}
