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

namespace local_nitro\mcp;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Tests for JSON Schema generation.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(schema::class)]
final class schema_test extends \advanced_testcase {
    public function test_types_defaults_and_required(): void {
        $schema = schema::from_description(new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'dry_run' => new external_value(PARAM_BOOL, 'Preview only', VALUE_DEFAULT, false),
            'grade' => new external_value(PARAM_FLOAT, 'Points', VALUE_DEFAULT, 0),
            'text' => new external_value(PARAM_RAW, 'Markdown', VALUE_DEFAULT, null, NULL_ALLOWED),
            'section' => new external_single_structure([
                'number' => new external_value(PARAM_INT, 'Section number'),
            ], 'Target section'),
            'userids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'User ID'),
                'Recipients',
                VALUE_DEFAULT,
                []
            ),
        ]));

        $this->assertSame('object', $schema['type']);
        $this->assertSame(['courseid', 'section'], $schema['required']);
        $this->assertFalse($schema['additionalProperties']);
        $this->assertSame(['type' => 'integer', 'description' => 'Course ID'], $schema['properties']['courseid']);
        $this->assertSame(false, $schema['properties']['dry_run']['default']);
        $this->assertSame('boolean', $schema['properties']['dry_run']['type']);
        $this->assertSame('number', $schema['properties']['grade']['type']);
        $this->assertSame('string', $schema['properties']['text']['type']);
        $this->assertArrayNotHasKey('default', $schema['properties']['text']);
        $this->assertSame(['number'], $schema['properties']['section']['required']);
        $this->assertSame('array', $schema['properties']['userids']['type']);
        $this->assertSame('integer', $schema['properties']['userids']['items']['type']);
    }

    public function test_empty_parameters_encode_as_object(): void {
        $json = json_encode(schema::from_description(new external_function_parameters([])));
        $this->assertSame('{"type":"object","properties":{},"additionalProperties":false}', $json);
    }
}
