<?php

namespace WebSK\CRUD;

use WebSK\Utils\Sanitize;

class CRUDHtml
{
    /**
     * @param array<string, scalar|null> $tag_attribute_arr
     * @param string|\Closure(): void $html
     */
    public static function tag(string $tag_name, array $tag_attribute_arr, string|\Closure $html): string
    {
        if ($tag_name == '') {
            return '';
        }

        if (is_callable($html)) {
            ob_start();
            $html();
            $buffer = ob_get_clean();
            $html = $buffer === false ? '' : $buffer;
        }

        $tag_attributes = '';
        foreach ($tag_attribute_arr as $tag_attribute => $tag_attribute_str) {
            $tag_attributes .= ' ' . Sanitize::sanitizeAttrValue($tag_attribute) . '="'
                . Sanitize::sanitizeAttrValue($tag_attribute_str === null ? null : (string)$tag_attribute_str) . '" ';
        }

        return '<' . Sanitize::sanitizeAttrValue($tag_name) . ' ' . $tag_attributes . '>' . $html . '</' . Sanitize::sanitizeAttrValue($tag_name) . '>';
    }

    /**
     * @param array<string, scalar|null> $tag_attribute_arr
     * @param string|\Closure(): void $html
     */
    public static function echoTag(string $tag_name, array $tag_attribute_arr, string|\Closure $html): void
    {
        echo self::tag($tag_name, $tag_attribute_arr, $html);
    }

    public static function a(string $url, string $text, string $classes_str = ''): string
    {
        return self::tag('a', [
            'href' => Sanitize::sanitizeUrl($url),
            'class' => Sanitize::sanitizeAttrValue($classes_str)
        ], $text);
    }

    /** @param string|\Closure(): void $html */
    public static function div(string $css_class, string $id, string|\Closure $html): string
    {
        if (is_callable($html)) {
            ob_start();
            $html();
            $buffer = ob_get_clean();
            $html = $buffer === false ? '' : $buffer;
        }

        return self::tag('div', [
            'class' => $css_class,
            'id' => $id
        ], $html);
    }
}
