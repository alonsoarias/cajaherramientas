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
 * Caja de Herramientas block library functions.
 *
 * @package    block_cajaherramientas
 * @copyright  2024 Your Name or Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Serve the files from the Caja de Herramientas block.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function block_cajaherramientas_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB, $CFG;

    // Require login to access files
    require_login();

    // Ensure the context level is correct
    if ($context->contextlevel != CONTEXT_BLOCK) {
        return false;
    }

    // Check the file area
    if ($filearea !== 'backgroundimage') {
        return false;
    }

    // Extract itemid and filename from arguments
    $itemid = array_shift($args); // The itemid identifies the specific file
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    // Get the file storage object
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'block_cajaherramientas', $filearea, $itemid, $filepath, $filename);

    // Check if the file exists and is not a directory
    if (!$file || $file->is_directory()) {
        return false;
    }

    // Send the file
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}