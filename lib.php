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

/**
 * Library callbacks for Local Group Import.
 *
 * @package    local_groupimport
 * @copyright  2026 Kevin Jarniac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extends the course navigation with the Group Import entry (course "More" menu).
 *
 * @param navigation_node $coursenode The course navigation node.
 * @param stdClass $course The course record.
 * @param context_course $context The course context.
 * @return void
 */
function local_groupimport_extend_navigation_course(navigation_node $coursenode, stdClass $course,
        context_course $context): void {

    if (!has_capability('moodle/course:managegroups', $context)) {
        return;
    }

    $url = new moodle_url('/local/groupimport/index.php', ['id' => $course->id]);

    if ($coursenode->find('local_groupimport', navigation_node::TYPE_CUSTOM)) {
        return;
    }

    $coursenode->add(
        get_string('groupimport', 'local_groupimport'),
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'local_groupimport',
        new pix_icon('i/groups', '')
    );
}

/*
 * Note: do not define local_groupimport_extend_settings_navigation() here.
 * This plugin does not add administration navigation entries.
 */
