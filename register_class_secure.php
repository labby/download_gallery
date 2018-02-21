<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2018 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @license		GNU General Public License
 *  @license terms	see info.php of this module
 *  @platform		see info.php of this module
 *
 */


$files_to_register = array(
	'add_file.php',
	'add_group.php',
	'add.php',
	'change_active_status.php',
	'delete_file.php',
	'delete_group.php',
	'delete.php',
	'dlc.php',
	'help.php',
	'modify_extensions.php',
	'modify_file.php',
	'modify_group.php',
	'modify_settings.php',
	'move_down.php',
	'move_dragdrop.php',
	'move_up.php',
	'save.php',
	'save_extsettings.php',
	'save_file.php',
	'save_group.php',
	'save_settings.php',
	'update_sort.php'
);

LEPTON_secure::getInstance()->accessFiles( $files_to_register );

?>