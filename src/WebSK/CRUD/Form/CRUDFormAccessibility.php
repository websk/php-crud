<?php

namespace WebSK\CRUD\Form;

use WebSK\Utils\Sanitize;

final class CRUDFormAccessibility
{
    public static function associateFirstControl(string $html, string $label_id): string
    {
        $associated = false;
        $label_id = Sanitize::sanitizeAttrValue($label_id);

        return preg_replace_callback(
            '/<(input|select|textarea)\b[^>]*>/i',
            static function (array $matches) use (&$associated, $html, $label_id): string {
                $tag = $matches[0];
                if ($associated || preg_match('/\btype\s*=\s*(["\'])hidden\1/i', $tag)) {
                    return $tag;
                }

                if (preg_match('/\baria-(?:label|labelledby)\s*=/i', $tag)) {
                    $associated = true;
                    return $tag;
                }

                if (preg_match('/\bid\s*=\s*(["\'])([^"\']+)\1/i', $tag, $id_matches)) {
                    $control_id = preg_quote($id_matches[2], '/');
                    if (preg_match('/<label\b[^>]*\bfor\s*=\s*(["\'])' . $control_id . '\1/i', $html)) {
                        $associated = true;
                        return $tag;
                    }
                }

                $associated = true;
                return preg_replace(
                    '/\s*(\/?)>$/',
                    ' aria-labelledby="' . $label_id . '"$1>',
                    $tag,
                    1
                ) ?? $tag;
            },
            $html
        ) ?? $html;
    }
}
