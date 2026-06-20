<?php

if (! function_exists('render_rich_text_content')) {
    function render_rich_text_content(?string $value, string $class = 'rich-text-content'): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/<[a-z][\s\S]*>/i', $value) === 1) {
            $allowedTags = '<p><br><strong><b><em><i><u><s><ol><ul><li><h1><h2><h3><blockquote><table><thead><tbody><tr><th><td>';
            $html = strip_tags($value, $allowedTags);
            $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
            $html = preg_replace('/\s+(href|src|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
            // Rich text editors often append empty paragraphs (<p><br></p>) that create visible extra gaps.
            $html = preg_replace('/(?:<p>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>)+$/i', '', $html) ?? $html;
            $html = sivalid_normalize_rich_text_lists($html);

            return '<div class="' . esc($class, 'attr') . '" style="max-width:100%;overflow-x:auto;">' . $html . '</div>';
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

        return '<div class="' . esc($class, 'attr') . '" style="max-width:100%;overflow-x:auto;">' . implode('', $html) . '</div>';
    }
}

if (! function_exists('sivalid_normalize_rich_text_lists')) {
    function sivalid_normalize_rich_text_lists(string $html): string
    {
        if ($html === '' || stripos($html, '<ol') === false || ! class_exists('DOMDocument')) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previousUseInternalErrors = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="sivalid-rich-text-root">' . $html . '</div>',
            LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        if (! $loaded) {
            return $html;
        }

        $root = $dom->getElementById('sivalid-rich-text-root');
        if (! $root) {
            return $html;
        }

        sivalid_apply_nested_ordered_list_types($root);
        sivalid_apply_rich_text_table_layout($root);

        $previousTopLevelOlEnd = null;
        $hasContinuableBreak = false;

        foreach (iterator_to_array($root->childNodes) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if ($tag === 'table') {
                if ($previousTopLevelOlEnd !== null) {
                    $hasContinuableBreak = true;
                }

                continue;
            }

            if ($tag !== 'ol') {
                if (in_array($tag, ['h1', 'h2', 'h3', 'blockquote'], true)) {
                    $hasContinuableBreak = false;
                }

                continue;
            }

            if ($hasContinuableBreak && $previousTopLevelOlEnd !== null && ! $node->hasAttribute('start')) {
                $node->setAttribute('start', (string) ($previousTopLevelOlEnd + 1));
            }

            $start = $node->hasAttribute('start') ? max(1, (int) $node->getAttribute('start')) : 1;
            $itemCount = 0;

            foreach ($node->childNodes as $childNode) {
                if ($childNode instanceof DOMElement && strtolower($childNode->tagName) === 'li') {
                    $itemCount++;
                }
            }

            if ($itemCount > 0) {
                $previousTopLevelOlEnd = $start + $itemCount - 1;
            }

            $hasContinuableBreak = false;
        }

        $normalized = '';
        foreach ($root->childNodes as $childNode) {
            $normalized .= $dom->saveHTML($childNode);
        }

        return $normalized !== '' ? $normalized : $html;
    }
}

if (! function_exists('sivalid_apply_rich_text_table_layout')) {
    function sivalid_apply_rich_text_table_layout(DOMElement $root): void
    {
        $tables = $root->getElementsByTagName('table');
        foreach ($tables as $table) {
            $table->setAttribute('style', 'width:100%;table-layout:auto;border-collapse:collapse;margin:.7rem 0;background:#fff;');
        }

        foreach (['th', 'td'] as $tagName) {
            $cells = $root->getElementsByTagName($tagName);
            foreach ($cells as $cell) {
                $cell->setAttribute('style', 'min-width:120px;white-space:normal;word-break:normal;overflow-wrap:anywhere;vertical-align:top;');
            }
        }
    }
}

if (! function_exists('sivalid_apply_nested_ordered_list_types')) {
    function sivalid_apply_nested_ordered_list_types(DOMElement $root): void
    {
        $walker = static function (DOMElement $element, int $orderedListDepth) use (&$walker): void {
            $tag = strtolower($element->tagName);
            $nextDepth = $orderedListDepth + ($tag === 'ol' ? 1 : 0);

            if ($tag === 'ol' && $orderedListDepth > 0 && ! $element->hasAttribute('type')) {
                $element->setAttribute('type', $orderedListDepth === 1 ? 'a' : 'i');
            }

            foreach ($element->childNodes as $childNode) {
                if ($childNode instanceof DOMElement) {
                    $walker($childNode, $nextDepth);
                }
            }
        };

        foreach ($root->childNodes as $childNode) {
            if ($childNode instanceof DOMElement) {
                $walker($childNode, 0);
            }
        }
    }
}
