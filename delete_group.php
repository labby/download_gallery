<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
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

// Get id
$group_id = '';
if(!isset($_GET['group_id']) OR !is_numeric($_GET['group_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$group_id = (int) $_GET['group_id'];
}

// Include admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(LEPTON_PATH.'/modules/admin.php');

//move all fiels in group to no group
$database->query("UPDATE ".TABLE_PREFIX."mod_download_gallery_files SET group_id = '0',  active = '0' WHERE group_id = '$group_id' AND section_id = '$section_id'");

// Delete row
$database->query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE group_id = '$group_id' AND section_id = '$section_id'");

// Include the ordering class
require(LEPTON_PATH.'/framework/class.order.php');			
// Initialize order object 
$order = new order(TABLE_PREFIX."mod_download_gallery_groups", 'position', 'group_id', 'section_id');
// reorder all groups in this section_id
$order->clean( $section_id );   

// Check if there is a db error, otherwise say successful
$back_location = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;

if($database->is_error()) {
	$admin->print_error($database->get_error(), $back_location);
} else {
	$admin->print_success($TEXT['SUCCESS'], $back_location);
}

// Print admin footer
$admin->print_footer();

?>