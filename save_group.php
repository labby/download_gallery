<?php
/* 
 * Copyright and more information see file info.php
*/

require('../../config.php');

// Get id
$group_id = '';
if(!isset($_POST['group_id']) OR !is_numeric($_POST['group_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$group_id = (int) $_POST['group_id'];
}

if(!isset($_POST['active']) OR !is_numeric($_POST['active'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$active = (int) $_POST['active'];
}

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(LEPTON_PATH.'/modules/admin.php');

// Vagroup_idate all fields
if($admin->get_post('title') == '') {
	$admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], LEPTON_URL.'/modules/download_gallery/modify_group.php?page_id='.$page_id.'&section_id='.$section_id.'&group_id='.$group_id);
} else {
	$title = $admin->add_slashes(strip_tags($admin->get_post('title')));
}

// Update row
$database->query("UPDATE ".TABLE_PREFIX."mod_download_gallery_groups SET title = '$title', active = '$active' WHERE group_id = '$group_id' and page_id = '$page_id'");

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/download_gallery/modify_group.php?page_id='.$page_id.'&section_id='.$section_id.'&group_id='.$group_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
?>