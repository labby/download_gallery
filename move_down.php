<?php
/*
 * Copyright and more information see file info.php
 */
/*
	changes by Stefek:
	- added "reorder file position by group" (apply $common_field var)
		This is needed to ensure that files of every group 
		got their own ordering 
	- apply special id null reorder function
		(there is no such methode in the 'order' class
 */
require('../../config.php');

// Get id
$id = '';
if(!isset($_GET['file_id']) OR !is_numeric($_GET['file_id'])) {
	if(!isset($_GET['group_id']) OR !is_numeric($_GET['group_id'])) {
		header("Location: index.php");
	} else {
		$id = (int) $_GET['group_id'];
		$id_field = 'group_id';
		$table = TABLE_PREFIX.'mod_download_gallery_groups';
		$common_field = 'section_id';

	}
} else {
	$id = (int) $_GET['file_id'];
	$id_field = 'file_id';
	$table = TABLE_PREFIX.'mod_download_gallery_files';
	$common_field = 'group_id';

}

require(WB_PATH.'/modules/admin.php');				// Include WB admin wrapper script
require(WB_PATH.'/framework/class.order.php');			// Include the ordering class

// Create new order object and reorder
$order = new order($table, 'position', $id_field, $common_field);

if($order->move_down($id)) {
	
	/*
		the following handling will need a rework, maybe
		(the function is loaded in any case yet, even if not needed)
	*/
	// apply special id null reorder handling (load extern functions)
	require('functions.php');
	reorder_id_null_group(TABLE_PREFIX."mod_download_gallery_files", $section_id);	


	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_error($TEXT['ERROR'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>