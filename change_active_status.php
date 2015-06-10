<?php
require('../../config.php');

// Get ID
if (isset($_GET['file_id']) && is_numeric($_GET['file_id']) && isset($_GET['status']) && is_numeric($_GET['status'])) {
	
	$fID 	= $_GET['file_id'];
	$status = $_GET['status'];
	
}elseif (isset($_GET['group_id']) && is_numeric($_GET['group_id']) && isset($_GET['status']) && is_numeric($_GET['status'])) {
	
	$gID 	= $_GET['group_id'];
	$status = $_GET['status'];	
	
} else {

	exit(header("Location: ".ADMIN_URL."/pages/index.php"));	
}


// reverse current status to change its value in DB 
if($status == 1) $status = 0;
elseif($status == 0) $status = 1;

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

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

