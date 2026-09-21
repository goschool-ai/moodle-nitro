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
 * Site settings for local_nitro.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_nitro_category', new lang_string('pluginname', 'local_nitro')));
    $settings = new admin_settingpage('local_nitro', new lang_string('settings', 'local_nitro'));
    $ADMIN->add('local_nitro_category', $settings);
    $ADMIN->add('local_nitro_category', new admin_externalpage(
        'local_nitro_clients',
        new lang_string('clients', 'local_nitro'),
        new moodle_url('/local/nitro/clients.php')
    ));

    if ($ADMIN->fulltree) {
        if (\local_nitro\oauth\clients::cap_reached()) {
            $settings->add(new admin_setting_heading(
                'local_nitro/capwarning',
                '',
                $OUTPUT->notification(get_string(
                    'dcrcapreached',
                    'local_nitro',
                    \local_nitro\oauth\clients::count_never_used()
                ), \core\output\notification::NOTIFY_WARNING)
            ));
        }

        $settings->add(new \local_nitro\admin\setting_selfcheck());

        $settings->add(new admin_setting_configcheckbox(
            'local_nitro/active',
            new lang_string('enabled', 'local_nitro'),
            new lang_string('enabled_desc', 'local_nitro'),
            0
        ));

        $settings->add(new admin_setting_configmulticheckbox(
            'local_nitro/tools',
            new lang_string('tools', 'local_nitro'),
            new lang_string('tools_desc', 'local_nitro'),
            array_fill_keys(\local_nitro\local\tools::ALL, 1),
            \local_nitro\local\tools::choices()
        ));

        $settings->add(new admin_setting_heading(
            'local_nitro/oauthheading',
            new lang_string('oauthheading', 'local_nitro'),
            ''
        ));

        $settings->add(new admin_setting_configtextarea(
            'local_nitro/redirecturis',
            new lang_string('redirecturis', 'local_nitro'),
            new lang_string('redirecturis_desc', 'local_nitro'),
            implode("\n", [
                'https://claude.ai/api/mcp/auth_callback',
                'https://claude.com/api/mcp/auth_callback',
                'https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect',
                'http://localhost/callback',
                'http://127.0.0.1/callback',
            ]),
            PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtextarea(
            'local_nitro/cimdhosts',
            new lang_string('cimdhosts', 'local_nitro'),
            new lang_string('cimdhosts_desc', 'local_nitro'),
            'claude.ai',
            PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configcheckbox(
            'local_nitro/dcrenabled',
            new lang_string('dcrenabled', 'local_nitro'),
            new lang_string('dcrenabled_desc', 'local_nitro'),
            1
        ));

        $settings->add(new admin_setting_configtext(
            'local_nitro/dcrcap',
            new lang_string('dcrcap', 'local_nitro'),
            new lang_string('dcrcap_desc', 'local_nitro'),
            1000,
            PARAM_INT
        ));

        $settings->add(new admin_setting_heading(
            'local_nitro/feedbackheading',
            new lang_string('feedbackheading', 'local_nitro'),
            ''
        ));

        $settings->add(new admin_setting_configcheckbox(
            'local_nitro/feedbackenabled',
            new lang_string('feedbackenabled', 'local_nitro'),
            new lang_string('feedbackenabled_desc', 'local_nitro'),
            1
        ));

        $settings->add(new admin_setting_configtext(
            'local_nitro/feedbackemail',
            new lang_string('feedbackemail', 'local_nitro'),
            new lang_string('feedbackemail_desc', 'local_nitro'),
            'info@goschool.ai',
            PARAM_EMAIL
        ));

        $settings->add(new admin_setting_configtextarea(
            'local_nitro/consentnotice',
            new lang_string('consentnotice', 'local_nitro'),
            new lang_string('consentnotice_desc', 'local_nitro'),
            '',
            PARAM_TEXT
        ));
    }
}
