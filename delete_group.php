<?php
/*  
 * Copyright and more information see file info.php
 */
/*
 changes by Stefek:
 
 - added reordering to reorder the group position after deleting a group correctly
 - changed the use of page_id to section_id in database queries
 
*/
 
 
require('../../config.php');

// Get id
$group_id = '';
if(!isset($_GET['group_id']) OR !is_numeric($_GET['group_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$group_id = (int) $_GET['group_id'];
}

// Include WB admin wrapper script
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