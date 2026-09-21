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
 * Tests for markdown conversion with the cleaning report, and for date handling.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(content::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(dates::class)]
final class content_test extends \advanced_testcase {
    public function test_headings_table_and_list(): void {
        $md = "# Követelmények\n\n## Beadandók\n\n- első\n- második\n\n| Hét | Téma |\n|-----|------|\n| 4 | Rekurzió |\n";
        $result = content::from_markdown($md);
        foreach (['<h1', '<h2', '<ul>', '<li>első</li>', '<table', '<td>Rekurzió</td>'] as $fragment) {
            $this->assertStringContainsString($fragment, $result['html']);
        }
        $this->assertSame([], $result['removed']);
    }

    public function test_removed_content_is_reported(): void {
        $md = "Ábra:\n\n<svg width=\"10\"><circle r=\"4\"/></svg>\n\n<script>alert(1)</script>\n\n"
            . "<a href=\"https://x.example\" onclick=\"steal()\">link</a>\n";
        $result = content::from_markdown($md);
        $this->assertStringNotContainsString('<script', $result['html']);
        $this->assertStringNotContainsString('onclick', $result['html']);
        $this->assertStringContainsString('link', $result['html']);
        $report = implode('; ', $result['removed']);
        $this->assertStringContainsString('<script> element', $report);
        $this->assertStringContainsString('<svg> element', $report);
        $this->assertStringContainsString('onclick attribute on <a>', $report);
    }

    public function test_date_without_offset_uses_user_time_zone(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user(['timezone' => 'Europe/Budapest', 'lang' => 'en']));
        $ts = dates::parse('2026-10-09T23:59', 'due');
        $this->assertSame(['date' => '2026-10-09T23:59:00+02:00', 'weekday' => 'Friday'], dates::describe($ts));
    }

    public function test_date_with_offset(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user(['timezone' => 'Europe/Budapest']));
        $this->assertSame(dates::parse('2026-10-09T21:59:00Z', 'due'), dates::parse('2026-10-09T23:59:00+02:00', 'due'));
        $this->assertSame(dates::parse('2026-10-09 23:59', 'due'), dates::parse('2026-10-09T23:59:00', 'due'));
    }

    /**
     * Values that are not ISO 8601 date-times.
     *
     * @return array
     */
    public static function bad_date_provider(): array {
        return [['next friday'], ['2026-10-09'], ['09/10/2026 23:59'], ['2026-02-30T10:00'], ['2026-13-01T10:00'], ['']];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('bad_date_provider')]
    public function test_bad_date(string $value): void {
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches('/due: .* ISO 8601/');
        dates::parse($value, 'due');
    }
}
