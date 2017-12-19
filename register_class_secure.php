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

global $lepton_filemanager;
if (!is_object($lepton_filemanager)) require_once( "../../framework/class.lepton.filemanager.php" );

$basename = "/modules/download_gallery/";
$files_to_register = array(
	$basename.'add_file.php',
	$basename.'add_group.php',
	$basename.'add.php',
	$basename.'change_active_status.php',
	$basename.'delete_file.php',
	$basename.'delete_group.php',
	$basename.'delete.php',
	$basename.'dlc.php',
	$basename.'help.php',
	$basename.'modify_extensions.php',
	$basename.'modify_file.php',
	$basename.'modify_group.php',
	$basename.'modify_settings.php',
	$basename.'move_down.php',
	$basename.'move_dragdrop.php',
	$basename.'move_up.php',
	$basename.'save.php',
	$basename.'save_extsettings.php',
	$basename.'save_file.php',
	$basename.'save_group.php',
	$basename.'save_settings.php',
	$basename.'update_sort.php'
);

$lepton_filemanager->register( $files_to_register );

?>