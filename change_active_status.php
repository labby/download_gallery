<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @license		GNU General Public License
 *  @license terms	see info.php of this module
 *  @platform		see info.php of this module
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'LEPTON_PATH' ) )
{
    include( LEPTON_PATH . '/framework/class.secure.php' );
} 
else
{
    $oneback = "../";
    $root    = $oneback;
    $level   = 1;
    while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) )
    {
        $root .= $oneback;
        $level += 1;
    } 
    if ( file_exists( $root . '/framework/class.secure.php' ) )
    {
        include( $root . '/framework/class.secure.php' );
    } 
    else
    {
        trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
    }
}
// end include class.secure.php
// Get ID
if (isset($_GET['file_id']) && is_numeric($_GET['file_id']) && isset($_GET['status']) && is_numeric($_GET['status'])) {
	
	$fID 	= intval($_GET['file_id']);
	$status = intval($_GET['status']);
	
}elseif (isset($_GET['group_id']) && is_numeric($_GET['group_id']) && isset($_GET['status']) && is_numeric($_GET['status'])) {
	
	$gID 	= intval($_GET['group_id']);
	$status = intval($_GET['status']);	
	
} else {

	exit(header("Location: ".ADMIN_URL."/pages/index.php"));	
}

// reverse current status to change its value in DB 
$status = ($status == 1) ? 0 : 1;

// Include admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(LEPTON_PATH.'/modules/admin.php');

// change FILE STATUS?
if (isset($fID) && is_numeric($fID))
	$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET `active` = '$status' WHERE `file_id` ='$fID'");
	
// change GROUP STATUS?
if (isset($gID) && is_numeric($gID))
	$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_groups` SET `active` = '$status' WHERE `group_id` ='$gID'");
	

/**
	return to modify.php
*/
if($database->is_error()) {
	$admin->print_error(
			$database->get_error(),  
			ADMIN_URL.'/pages/modify.php?page_id='.$page_id
		);
} else {
	$admin->print_success(
			$TEXT['SUCCESS'],  
			ADMIN_URL.'/pages/modify.php?page_id='.$page_id
		);
}

// Print admin footer
$admin->print_footer();

