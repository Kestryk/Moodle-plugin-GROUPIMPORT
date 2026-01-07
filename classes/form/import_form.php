<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_groupimport\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Import form for Local Group Import.
 *
 * @package    local_groupimport
 * @copyright  2026 Kevin Jarniac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_form extends \moodleform {

    /**
     * Defines the form elements.
     *
     * @return void
     */
    public function definition(): void {
        global $DB;

        $mform = $this->_form;

        // Retrieve the course id passed via customdata.
        $courseid = (int)($this->_customdata['courseid'] ?? 0);

        // Hidden field to return the course id on submit.
        $mform->addElement('hidden', 'id', $courseid);
        $mform->setType('id', PARAM_INT);

        // CSV file.
        $mform->addElement(
            'filepicker',
            'importfile',
            get_string('importfile', 'local_groupimport'),
            null,
            ['accepted_types' => ['.csv']]
        );
        $mform->addRule('importfile', null, 'required', null, 'client');

        // User identification field selection.
        $config = get_config('local_groupimport');

        // All possible fields.
        $alloptions = [
            'username' => get_string('username'),
            'email' => get_string('email'),
            'idnumber' => get_string('idnumber'),
        ];

        $customfields = $DB->get_records('user_info_field', null, 'name ASC');
        foreach ($customfields as $field) {
            $key = 'profile_field_' . $field->shortname;
            $alloptions[$key] = format_string($field->name);
        }

        // Fields allowed in admin settings.
        if (!empty($config->alloweduserfields)) {
            $allowed = array_map('trim', explode(',', $config->alloweduserfields));
            $allowed = array_filter($allowed, static function(string $value): bool {
                return $value !== '';
            });
        } else {
            // Fallback: username only.
            $allowed = ['username'];
        }

        // Keep only allowed fields that still exist.
        $options = array_intersect_key($alloptions, array_flip($allowed));

        // Ensure there is always at least one option.
        if (empty($options)) {
            $options = ['username' => get_string('username')];
            $allowed = ['username'];
        }

        // Default field.
        $defaultuserfield = 'username';
        if (!empty($config->defaultuserfield) && isset($options[$config->defaultuserfield])) {
            $defaultuserfield = $config->defaultuserfield;
        } else if (!empty($allowed) && isset($options[reset($allowed)])) {
            $defaultuserfield = reset($allowed);
        }

        $mform->addElement(
            'select',
            'userfield',
            get_string('userfield', 'local_groupimport'),
            $options
        );
        $mform->setDefault('userfield', $defaultuserfield);
        $mform->addHelpButton('userfield', 'userfield', 'local_groupimport');

        // Submit button.
        $this->add_action_buttons(false, get_string('submitimport', 'local_groupimport'));
    }
}
