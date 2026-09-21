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

namespace local_nitro\admin;

use local_nitro\local\selfcheck;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Settings page element that runs and shows the discovery self-check. It stores nothing.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_selfcheck extends \admin_setting {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('local_nitro/selfcheck', new \lang_string('selfcheck', 'local_nitro'), '', '');
    }

    #[\Override]
    public function get_setting() {
        return true;
    }

    #[\Override]
    public function write_setting($data) {
        return '';
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;
        if (!\local_nitro\local\plugin::active()) {
            return format_admin_setting(
                $this,
                $this->visiblename,
                $OUTPUT->notification(
                    get_string('selfcheckinactive', 'local_nitro'),
                    \core\output\notification::NOTIFY_INFO,
                    false
                ),
                '',
                false,
                '',
                '',
                $query
            );
        }
        $results = selfcheck::run();
        $rows = [];
        $rootmissing = false;
        foreach ($results as $result) {
            $rootmissing = $rootmissing || ($result['root'] && !$result['ok']);
            $rows[] = [
                'url' => $result['url'],
                'label' => get_string('selfcheck_' . $result['kind'] . ($result['root'] ? '_root' : ''), 'local_nitro'),
                'ok' => $result['ok'],
                'status' => $result['status'],
            ];
        }
        $rules = selfcheck::rewrite_rules();
        $server = selfcheck::web_server();
        $order = $server === 'nginx' ? ['nginx', 'apache', 'htaccess'] : ['apache', 'htaccess', 'nginx'];
        $html = $OUTPUT->render_from_template('local_nitro/selfcheck', [
            'rows' => $rows,
            'pluginok' => !array_filter($results, fn($r) => !$r['root'] && !$r['ok']),
            'rootmissing' => $rootmissing,
            'rules' => array_map(fn($k) => ['name' => get_string('selfcheckrule_' . $k, 'local_nitro'),
                'rule' => $rules[$k]], $order),
        ]);
        return format_admin_setting($this, $this->visiblename, $html, '', false, '', '', $query);
    }
}
