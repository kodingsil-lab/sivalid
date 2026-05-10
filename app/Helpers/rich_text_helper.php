<?php

if (! function_exists('render_rich_text_content')) {
    function render_rich_text_content(?string $value, string $class = 'ql-editor rich-text-content'): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/<[a-z][\s\S]*>/i', $value) === 1) {
            $allowedTags = '<p><br><strong><b><em><i><u><s><ol><ul><li><h1><h2><h3><blockquote>';
            $html = strip_tags($value, $allowedTags);
            $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
            $html = preg_replace('/\s+(href|src|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
            // Quill often appends empty paragraphs (<p><br></p>) that create visible extra gaps.
            $html = preg_replace('/(?:<p>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>)+$/i', '', $html) ?? $html;

            return '<div class="' . esc($class, 'attr') . '">' . $html . '</div>';
        }

        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $paragraphs = preg_split('/\n{2,}/', $value) ?: [];
        $html = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph !== '') {
                $html[] = '<p>' . nl2br(esc($paragraph)) . '</p>';
            }
        }

        return '<div class="' . esc($class, 'attr') . '">' . implode('', $html) . '</div>';
    }
}

