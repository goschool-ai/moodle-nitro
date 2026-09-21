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
 * Builds the template demo course and saves it as the sandbox template backup.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options] = cli_get_params(['category' => 1, 'help' => false], ['h' => 'help']);
if ($options['help']) {
    cli_writeln("Builds the nitro demo template course and saves it as the sandbox template.\n\n"
        . "Options:\n  --category=ID  course category for the template course (default 1)");
    exit(0);
}

\core\session\manager::set_user(get_admin());
$courseid = \local_nitrosandbox\template::build((int) $options['category']);
cli_writeln("Template course {$courseid} built.");
$file = \local_nitrosandbox\template::save_backup($courseid);
cli_writeln('Saved ' . $file->get_filename() . ' (' . display_size($file->get_filesize()) . ').');
