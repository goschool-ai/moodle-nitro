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
 * English strings for local_nitrosandbox.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['category'] = 'Category for demo courses';
$string['category_desc'] = 'ID of the course category where personal demo courses are created.';
$string['enabled'] = 'Create demo courses on signup';
$string['enabled_desc'] = 'When a confirmed user logs in for the first time, create their personal demo course with fictitious students. Sandbox only.';
$string['onboardfrom'] = 'Onboard accounts created after';
$string['onboardfrom_desc'] = 'Unix timestamp: only accounts created after this moment get a demo course. Set to the moment you switch onboarding on, so the site\'s existing users are left alone. 0 means every account.';
$string['pluginname'] = 'nitro sandbox onboarding';
$string['privacy:metadata:preference:courseid'] = 'The ID of the personal demo course created for the user.';
$string['restorefailed'] = 'The demo course template could not be restored: {$a}';
$string['students'] = 'Fictitious students per demo course';
$string['template'] = 'Demo course template';
$string['template_desc'] = 'Backup (.mbz, without user data) restored for every new demo course. Build it with local/nitrosandbox/cli/build_template.php.';
$string['templatemissing'] = 'There is no demo course template. Run local/nitrosandbox/cli/build_template.php first.';
$string['usergone'] = 'The account no longer exists, so no demo course was created.';
