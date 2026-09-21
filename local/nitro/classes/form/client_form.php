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

namespace local_nitro\form;

use local_nitro\oauth\redirect_uris;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for registering an OAuth client by hand.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_form extends \moodleform {
    #[\Override]
    protected function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'name', get_string('clientname', 'local_nitro'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', null, 'maxlength', 255, 'client');

        $mform->addElement(
            'textarea',
            'redirecturis',
            get_string('clientredirecturis', 'local_nitro'),
            ['rows' => 4, 'cols' => 60]
        );
        $mform->setType('redirecturis', PARAM_RAW_TRIMMED);
        $mform->addRule('redirecturis', null, 'required', null, 'client');
        $mform->addHelpButton('redirecturis', 'clientredirecturis', 'local_nitro');

        $mform->addElement(
            'advcheckbox',
            'confidential',
            get_string('clientconfidential', 'local_nitro'),
            get_string('clientconfidential_label', 'local_nitro')
        );
        $mform->setDefault('confidential', 1);

        $this->add_action_buttons(true, get_string('clientregister', 'local_nitro'));
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $uris = self::split_uris($data['redirecturis'] ?? '');
        if (!$uris) {
            $errors['redirecturis'] = get_string('required');
        }
        foreach ($uris as $uri) {
            if (!redirect_uris::is_allowed($uri)) {
                $errors['redirecturis'] = get_string('clientredirectnotallowed', 'local_nitro', s($uri));
                break;
            }
        }
        return $errors;
    }

    /**
     * Redirect URIs from the textarea, one per line.
     *
     * @param string $text
     * @return string[]
     */
    public static function split_uris(string $text): array {
        return array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/', $text)))));
    }
}
