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

namespace mod_peerwork;


/**
 * Resets Peer Work activity.
 *
 * This only removes *user* data, not any configuration.
 *
 * The reset removes *all* Peerwork activities data, not specific ones.* This is justified on the basis that most likely removing users as part of reset.
 *
 * At present it does not perform any date shifts.
 */
class reset_helper {

    public static function reset_form(&$mform) {
        $mform->addElement('header','modpeerworkheader', get_string('modulenameplural', 'mod_peerwork'));
        $mform->addElement('static', 'modpeerworkdelete', get_string('delete'));
        $name = get_string('deleteallstudentdata', 'mod_peerwork');
        $mform->addElement('advcheckbox', 'reset_peerwork', $name);
    }

    /**
     * Course reset form defaults.
     * @param  object $course
     * @return array
     */
    public static function reset_course_form_defaults($course) {
        return [
            'reset_peerwork' => 1,
        ];
    }
    public static function reset_course_module($data) {
        global $DB;

        $status = [];

        if (!empty($data->reset_peerwork)) {
            $courseid = $data->courseid;
            // We're wrapping all of this in a transaction.
            $tx = $DB->start_delegated_transaction();
            reset_submissions($courseid);
            reset_peers($courseid);
            reset_justifications($courseid);
            reset_grades($courseid);
            $DB->commit_delegated_transaction($tx);
        }

        return $status;
    }


// Reset the peerwork_submission table.
    function reset_submissions($courseid) {
        global $DB;
        $sql = "DELETE ps
            FROM {peerwork_submission} ps
            JOIN {peerwork} p ON ps.peerworkid = p.id
            WHERE p.course = :courseid";
        $DB->execute($sql, ['courseid' => $courseid]);
    }

// Reset the peerwork_peers table.
    function reset_peers($courseid) {
        global $DB;
        $sql = "DELETE pp
            FROM {peerwork_peers} pp
            JOIN {peerwork} p ON pp.peerwork = p.id
            WHERE p.course = :courseid";
        $DB->execute($sql, ['courseid' => $courseid]);
    }

// Reset the peerwork_criteria table.
    function reset_criteria($courseid) {
        global $DB;
        $sql = "DELETE pc
            FROM {peerwork_criteria} pc
            JOIN {peerwork} p ON pc.peerworkid = p.id
            WHERE p.course = :courseid";
        $DB->execute($sql, ['courseid' => $courseid]);
    }

// Reset the peerwork_justification table.
    function reset_justifications($courseid) {
        global $DB;
        $sql = "DELETE pj
            FROM {peerwork_justification} pj
            JOIN {peerwork} p ON pj.peerworkid = p.id
            WHERE p.course = :courseid";
        $DB->execute($sql, ['courseid' => $courseid]);
    }

// Reset the peerwork_grades table.
    function reset_grades($courseid) {
        global $DB;
        $sql = "DELETE pg
            FROM {peerwork_grades} pg
            JOIN {peerwork} p ON pg.peerworkid = p.id
            WHERE p.course = :courseid";
        $DB->execute($sql, ['courseid' => $courseid]);
    }

// Reset the peerwork_plugin_config table.
    function reset_plugin_config($courseid) {
        global $DB;
        $sql = "DELETE pc
            FROM {peerwork_plugin_config} pc
            JOIN {peerwork} p ON pc.peerwork = p.id
            WHERE p.course = :courseid";
        $DB->execute($sql, ['courseid' => $courseid]);
    }

}
