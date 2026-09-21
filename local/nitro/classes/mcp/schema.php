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

use core_external\external_description;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * JSON Schema for tool inputs, generated from external function parameter descriptions.
 *
 * Adapted from webservice_mcp's tool_provider (GPL v3, MohammadReza PourMohammad), with nested
 * required structures listed as required, empty objects encoded as {}, additionalProperties false
 * (Moodle rejects unexpected keys), and no nullable types.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schema {
    /**
     * Schema for a description.
     *
     * @param external_description $desc
     * @return array
     */
    public static function from_description(external_description $desc): array {
        if ($desc instanceof external_value) {
            // Nullability is not advertised: external_value allows null by default, so it would mark
            // nearly every field nullable, and clients should omit optional values rather than send null.
            $schema = ['type' => self::type($desc->type)];
            if ($desc->desc !== '' && $desc->desc !== null) {
                $schema['description'] = $desc->desc;
            }
            if ($desc->required == VALUE_DEFAULT && $desc->default !== null) {
                $schema['default'] = self::typed_default($desc->type, $desc->default);
            }
            return $schema;
        }
        if ($desc instanceof external_single_structure) {
            $properties = [];
            $required = [];
            foreach ($desc->keys as $key => $sub) {
                $properties[$key] = self::from_description($sub);
                if ($sub->required == VALUE_REQUIRED) {
                    $required[] = $key;
                }
            }
            $schema = ['type' => 'object', 'properties' => $properties ?: new \stdClass()];
            if ($required) {
                $schema['required'] = $required;
            }
            $schema['additionalProperties'] = false;
            if ($desc->desc !== '' && $desc->desc !== null) {
                $schema['description'] = $desc->desc;
            }
            return $schema;
        }
        if ($desc instanceof external_multiple_structure) {
            $schema = ['type' => 'array', 'items' => self::from_description($desc->content)];
            if ($desc->desc !== '' && $desc->desc !== null) {
                $schema['description'] = $desc->desc;
            }
            return $schema;
        }
        return ['type' => 'object'];
    }

    /**
     * JSON Schema type for a Moodle PARAM_ type.
     *
     * @param string $paramtype
     * @return string
     */
    private static function type(string $paramtype): string {
        return match ($paramtype) {
            PARAM_INT => 'integer',
            PARAM_FLOAT, PARAM_LOCALISEDFLOAT => 'number',
            PARAM_BOOL => 'boolean',
            default => 'string',
        };
    }

    /**
     * A default value with the JSON type the schema declares.
     *
     * @param string $paramtype
     * @param mixed $default
     * @return mixed
     */
    private static function typed_default(string $paramtype, mixed $default): mixed {
        return match (self::type($paramtype)) {
            'integer' => (int) $default,
            'number' => (float) $default,
            'boolean' => (bool) $default,
            default => (string) $default,
        };
    }
}
