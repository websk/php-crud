<?php

namespace WebSK\CRUD;

use WebSK\Utils\Sanitize;

class CRUDHtml
{
    /** @var list<string> */
    private const array VOID_TAGS = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    /**
     * @param array<string, scalar|null> $tag_attribute_arr
     * @param string|\Closure(): void $html
     */
    public static function tag(string $tag_name, array $tag_attribute_arr, string|\Closure $html = ''): string
    {
        if ($tag_name == '') {
            return '';
        }

        if ($html instanceof \Closure) {
            ob_start();
            $html();
            $buffer = ob_get_clean();
            $html = $buffer === false ? '' : $buffer;
        }

        $tag_attributes = [];
        foreach ($tag_attribute_arr as $tag_attribute => $tag_attribute_str) {
            if ($tag_attribute_str === null) {
                continue;
            }

            $tag_attributes[] = Sanitize::sanitizeAttrValue($tag_attribute) . '="'
                . Sanitize::sanitizeAttrValue((string)$tag_attribute_str) . '"';
        }

        $safe_tag_name = Sanitize::sanitizeAttrValue($tag_name);
        $opening_tag = '<' . $safe_tag_name;
        if ($tag_attributes !== []) {
            $opening_tag .= ' ' . implode(' ', $tag_attributes);
        }
        $opening_tag .= '>';

        if (in_array(strtolower($tag_name), self::VOID_TAGS, true)) {
            return $opening_tag;
        }

        return $opening_tag . $html . '</' . $safe_tag_name . '>';
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
        $attributes = ['href' => Sanitize::sanitizeUrl($url)];
        if ($classes_str !== '') {
            $attributes['class'] = Sanitize::sanitizeAttrValue($classes_str);
        }

        return self::tag('a', $attributes, $text);
    }

    /** @param string|\Closure(): void $html */
    public static function div(string $css_class, string $id, string|\Closure $html): string
    {
        if ($html instanceof \Closure) {
            ob_start();
            $html();
            $buffer = ob_get_clean();
            $html = $buffer === false ? '' : $buffer;
        }

        $attributes = [];
        if ($css_class !== '') {
            $attributes['class'] = $css_class;
        }
        if ($id !== '') {
            $attributes['id'] = $id;
        }

        return self::tag('div', $attributes, $html);
    }
}
