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

namespace local_listcoursefiles\components;

use local_listcoursefiles\course_file;

/**
 * Class mod_feedback
 * @package local_listcoursefiles\components
 */
class mod_feedback extends course_file {
    /**
     * Try to get the download url for a file.
     *
     * @param array $file
     * @return null|\moodle_url
     */
    public function get_file_download_url($file) {
        if ($file->filearea == 'intro') {
            return new \moodle_url('/pluginfile.php/' . $file->contextid . '/' . $file->component . '/' .
                $file->filearea . '/' . $file->filepath . $file->filename);
        }

        return null;
    }

    public function get_edit_url($file) {
        global $DB;
        $url = '';
        if ($file->filearea === 'intro') { // Just checking description for now.
            $url = parent::get_edit_url($file);
        }

        return $url;
    }

    /**
     * Checks if embedded files have been used
     *
     * @param object $file
     * @param integer $courseid
     * @return bool
     */
    public function is_file_used($file, $courseid) {
        // File areas = intro, item, page_after_submit.
        global $DB;
        if ($file->filearea === 'item') {
            $item = $DB->get_record('feedback_item', ['id' => $file->itemid]);
            $isused = $this->is_embedded_file_used($item, 'presentation', $file->filename);
            return $isused;
        } else if ($file->filearea = 'page_after_submit') {
            $sql = 'SELECT m.* FROM {feedback} m
                    JOIN {course_modules} cm ON cm.instance = m.id
                    JOIN {context} ctx ON ctx.instanceid = cm.id
                    WHERE ctx.id = ?';
            $feedback = $DB->get_record_sql($sql, [$file->contextid]);
            $isused = $this->is_embedded_file_used($feedback, 'page_after_submit', $file->filename);
            return $isused;
        } else {
            return parent::is_file_used($file, $courseid);
        }
    }
}