<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_nitro\local;

/**
 * Markdown content for activities: Moodle's markdown conversion, then Moodle's text cleaning,
 * with a report of what the cleaning removed.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content {
    /**
     * Converts markdown to the HTML that will be stored.
     *
     * @param string $markdown
     * @return array{html: string, removed: string[]} cleaned HTML and a description of each removed item
     */
    public static function from_markdown(string $markdown): array {
        $html = markdown_to_html($markdown);
        $clean = clean_text($html, FORMAT_HTML);
        return ['html' => $clean, 'removed' => self::removed($html, $clean)];
    }

    /**
     * The plain text version of a markdown message.
     *
     * Moodle's html_to_text() upper-cases bold text and drops single line breaks, so a teacher's mail
     * arrives shouting and with the signature on one line. Markdown is already readable as it stands,
     * so the plain version is the source with its emphasis marks and link syntax taken out.
     *
     * @param string $markdown
     * @return string
     */
    public static function to_plain(string $markdown): string {
        $text = str_replace("\r\n", "\n", $markdown);
        $text = preg_replace('/^[ \t]{0,3}#{1,6}[ \t]*/m', '', $text);
        $text = preg_replace('/\*\*(.+?)\*\*/s', '$1', $text);
        $text = preg_replace('/(?<!\*)\*(?!\s)([^*]+?)(?<!\s)\*(?!\*)/s', '$1', $text);
        $text = preg_replace('/\[([^\]]+)\]\(([^)\s]+)\)/', '$1 ($2)', $text);
        return trim($text);
    }

    /**
     * What cleaning removed: elements and attributes present before and missing after.
     *
     * @param string $before
     * @param string $after
     * @return string[] for example "<script> element (1)", "onclick attribute on <a> (2)"
     */
    public static function removed(string $before, string $after): array {
        $was = self::inventory($before);
        $is = self::inventory($after);
        $removed = [];
        foreach ($was as $item => $count) {
            $lost = $count - ($is[$item] ?? 0);
            if ($lost > 0) {
                $removed[] = "{$item} ({$lost})";
            }
        }
        return $removed;
    }

    /**
     * Counts elements and element attributes in an HTML fragment.
     *
     * @param string $html
     * @return array<string, int>
     */
    private static function inventory(string $html): array {
        $counts = [];
        if (trim($html) === '') {
            return $counts;
        }
        $doc = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        foreach ((new \DOMXPath($doc))->query('//*') as $node) {
            $name = strtolower($node->nodeName);
            $element = "<{$name}> element";
            $counts[$element] = ($counts[$element] ?? 0) + 1;
            foreach ($node->attributes as $attribute) {
                $key = strtolower($attribute->nodeName) . " attribute on <{$name}>";
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }
        }
        return $counts;
    }
}
