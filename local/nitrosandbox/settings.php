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

/**
 * Settings for local_nitrosandbox.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_nitrosandbox', new lang_string('pluginname', 'local_nitrosandbox'));
    $ADMIN->add('localplugins', $settings);
    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configcheckbox(
            'local_nitrosandbox/enabled',
            new lang_string('enabled', 'local_nitrosandbox'),
            new lang_string('enabled_desc', 'local_nitrosandbox'),
            0
        ));
        $settings->add(new admin_setting_configtext(
            'local_nitrosandbox/onboardfrom',
            new lang_string('onboardfrom', 'local_nitrosandbox'),
            new lang_string('onboardfrom_desc', 'local_nitrosandbox'),
            0,
            PARAM_INT
        ));
        $settings->add(new admin_setting_configstoredfile(
            'local_nitrosandbox/template',
            new lang_string('template', 'local_nitrosandbox'),
            new lang_string('template_desc', 'local_nitrosandbox'),
            'template',
            0,
            ['accepted_types' => ['.mbz'], 'maxfiles' => 1]
        ));
        $settings->add(new admin_setting_configtext(
            'local_nitrosandbox/categoryid',
            new lang_string('category', 'local_nitrosandbox'),
            new lang_string('category_desc', 'local_nitrosandbox'),
            1,
            PARAM_INT
        ));
        $settings->add(new admin_setting_configtext(
            'local_nitrosandbox/students',
            new lang_string('students', 'local_nitrosandbox'),
            '',
            20,
            PARAM_INT
        ));
    }
}
