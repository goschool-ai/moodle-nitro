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
 * Activities identified by a stable key (the course module ID number): lookup, creation, and
 * partial updates that keep every setting the call does not name.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modules {
    /**
     * Finds the activity with a key in a course.
     *
     * @param int $courseid
     * @param string $key
     * @return \cm_info|null
     */
    public static function find_by_key(int $courseid, string $key): ?\cm_info {
        global $DB;
        $cmid = $DB->get_field(
            'course_modules',
            'id',
            ['course' => $courseid, 'idnumber' => $key, 'deletioninprogress' => 0],
            IGNORE_MULTIPLE
        );
        return $cmid ? get_fast_modinfo($courseid)->get_cm($cmid) : null;
    }

    /**
     * Validates a key.
     *
     * @param string $key
     * @return string
     */
    public static function check_key(string $key): string {
        $key = trim($key);
        if ($key === '' || \core_text::strlen($key) > 100) {
            throw new \invalid_parameter_exception('key must be 1 to 100 characters, for example week4-requirements.');
        }
        return $key;
    }

    /**
     * Checks that a section number exists.
     *
     * @param int $courseid
     * @param int $section
     */
    public static function check_section(int $courseid, int $section): void {
        $sections = get_fast_modinfo($courseid)->get_section_info_all();
        $highest = max(array_keys($sections));
        if ($section < 0 || !isset($sections[$section])) {
            throw new \invalid_parameter_exception("Section {$section} does not exist in this course; the highest "
                . "existing section number is {$highest}. Nothing was saved.");
        }
    }

    /**
     * Form data for updating an activity, as the edit form would submit it unchanged.
     *
     * get_moduleinfo_data() leaves out what each module's form adds in data_preprocessing();
     * without it update_moduleinfo() would reset those settings, for example switching off every
     * assignment submission type. So the module-specific part is added here.
     *
     * @param \cm_info $cm
     * @return \stdClass
     */
    public static function current_data(\cm_info $cm): \stdClass {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');
        $course = get_course($cm->course);
        [, $context, , $data] = get_moduleinfo_data($cm->get_course_module_record(true), $course);
        $data->introeditor['itemid'] = 0;

        switch ($cm->modname) {
            case 'page':
                $data->page = ['text' => $data->content, 'format' => $data->contentformat, 'itemid' => 0];
                $options = (array) unserialize_array((string) $data->displayoptions);
                foreach (['printintro', 'printlastmodified', 'popupwidth', 'popupheight'] as $option) {
                    $data->$option = $options[$option] ?? 0;
                }
                break;
            case 'assign':
                require_once($CFG->dirroot . '/mod/assign/locallib.php');
                require_once($CFG->libdir . '/formslib.php');
                $assign = new \assign($context, $cm, $course);
                $values = (array) $data;
                $assign->plugin_data_preprocessing($values);
                // Plugin settings (enabled flags, file limits, word limits...) are form defaults that each
                // plugin sets in get_settings(); build the settings form to read them, as the edit form would.
                $mform = new \MoodleQuickForm('local_nitro_assign_settings', 'post', '');
                $assign->add_all_plugin_settings($mform);
                foreach ($mform->_defaultValues as $name => $value) {
                    if (!array_key_exists($name, $values)) {
                        $values[$name] = $value;
                    }
                }
                // Plugins that are not configurable only add a hidden field, so set every state explicitly.
                foreach (array_merge($assign->get_submission_plugins(), $assign->get_feedback_plugins()) as $plugin) {
                    $values[$plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled'] = (int) $plugin->is_enabled();
                }
                $data = (object) $values;
                break;
        }
        return $data;
    }

    /**
     * Saves changed form data of an existing activity.
     *
     * @param \cm_info $cm
     * @param \stdClass $data from current_data(), with the call's changes applied
     */
    public static function update(\cm_info $cm, \stdClass $data): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');
        [$cm] = can_update_moduleinfo($cm->get_course_module_record(true));
        update_moduleinfo($cm, $data, get_course($cm->course));
    }

    /**
     * Creates an activity and returns its course module.
     *
     * @param \stdClass $data complete form data (modulename, course, section, visible, introeditor, ...)
     * @return \cm_info
     */
    public static function create(\stdClass $data): \cm_info {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $created = create_module($data);
        return get_fast_modinfo($data->course)->get_cm($created->coursemodule);
    }

    /**
     * Fields whose values differ between two records.
     *
     * @param \stdClass|array $before
     * @param \stdClass|array $after
     * @param string[] $fields
     * @return string[]
     */
    public static function changed($before, $after, array $fields): array {
        $before = (array) $before;
        $after = (array) $after;
        return array_values(array_filter(
            $fields,
            fn($field) => (string) ($before[$field] ?? '') !== (string) ($after[$field] ?? '')
        ));
    }
}
