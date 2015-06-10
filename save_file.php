<?php
/* 
 * Copyright and more information see file info.php
 */
 /*
	changes by Stefek:
	- added "reorder file position by group" 
		This is needed to ensure that files of every group 
		got their own ordering 
	- apply special id null reorder function
		(there is no such methode in the 'order' class
 */
require('../../config.php');
require_once(WB_PATH.'/framework/functions.php');
$update_when_modified = true; 					// Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');			// Include WB admin wrapper script
include_once(WB_PATH.'/modules/download_gallery/functions.php');

// Get id
$id = ''; $file_id = '';
if (!isset($_POST['file_id']) OR !is_numeric($_POST['file_id'])
or  !isset($_POST['active']) OR !is_numeric($_POST['active'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$id = (int) $_POST['file_id'];
	$file_id = $id;
	$active = $admin->get_post('active');
}

// STEP 0:	initialize some variables
$filename = '';
$fname = '';
$fileext = '';

// Validate all fields
if($admin->get_post('title') == '' AND $admin->get_post('url') == '') {
	$admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], WB_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$id);
} else {
	$title = $admin->add_slashes(htmlspecialchars($admin->get_post('title')));
	$description = 	$admin->add_slashes($admin->get_post('description'));
	$old_link = 	$admin->add_slashes($admin->get_post('link'));
	$existingfile = $admin->add_slashes($admin->get_post('existingfile'));
	$group = 		$admin->add_slashes($admin->get_post('group'));
	$overwrite = 	$admin->add_slashes($admin->get_post('overwrite'));
	$remotelink = 	$admin->add_slashes($admin->get_post('remote_link'));
	if(($existingfile=="") AND ($remotelink=="")) $existingfile=$old_link;
}

// Get page link URL
$query_page = $database->query("SELECT `level`, `link` FROM `".TABLE_PREFIX."pages` WHERE `page_id` = '$page_id'");
$page = $query_page->fetchRow();
$page_level = $page['level'];
$page_link = $page['link'];

// Check if the user uploaded an file or wants to delete one
if ((isset($_FILES['file']['tmp_name'])) AND ($_FILES['file']['tmp_name'] != '') AND ($existingfile == '')) {
	// Get real filename and set new filename
	$filename = trim($_FILES['file']['name']);
	$path_parts = pathinfo($filename);
	$fileext = strtolower($path_parts['extension']);
	$new_filename = WB_PATH.MEDIA_DIRECTORY.'/download_gallery/'.$filename;

	// Work-out what the link should be
	$file_link = WB_URL.MEDIA_DIRECTORY.'/download_gallery/'.$filename;
	if($overwrite=="yes" or !file_exists(WB_PATH.MEDIA_DIRECTORY.'/download_gallery/' .$filename)) {
		// Upload file
		move_uploaded_file($_FILES['file']['tmp_name'], $new_filename);
		change_mode($new_filename);
	}

	// update file information in the database
	$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET 
												`extension` = '$fileext', 
												`filename` = '$filename' 
												WHERE `file_id` = '$file_id' 
												AND `page_id` = '$page_id'");
}

// Check if the user uploaded a remote link
if ((isset($_POST['remote_link'])) AND ($_POST['remote_link'] != '') AND ($filename=='')) {
	// Get real filename and set new filename
	$filename = trim($remotelink);
	$path_parts = pathinfo($filename);
	$fileext = strtolower($path_parts['extension']);
	$new_filename = $filename;

	// Work-out what the link should be
	$file_link = $filename;

	// update file information in the database
	$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET 
							`extension` = '$fileext', 
							`filename` = '$filename' 
							WHERE `file_id` = '$file_id' 
							AND `page_id` = '$page_id'");
}

if ((isset($_POST['delete_file']) AND $_POST['delete_file'] != '')or(isset($_POST['delete_file2']) AND $_POST['delete_file2'] != '')) {
	// query the database for the file extension
	$query_content = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE `file_id` = '$file_id' AND `page_id` = '$page_id'");
	$fetch_content = $query_content->fetchRow();
	$fname = $fetch_content['filename'];
	$ext = $fetch_content['extension'];
	// Try unlinking file
	$query_duplicates = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE `filename` = '$fname' AND `extension` ='$ext' AND `page_id` = '$page_id'");
	$dups=$query_duplicates->numRows();
	//only delete the file if there is 1 database entry (not used on multiple sections)
	if(($dups==1)and (isset($_POST['delete_file']))) {
		$file = WB_PATH.MEDIA_DIRECTORY.'/download_gallery/' . $fname;
		if(file_exists($file) AND is_writable($file)) {
			unlink($file);
		}
	}
	//set variables so the fields are cleared so new file can be placed
	$file_link="";
	$filename="";
	$active=0;
}

if(trim($existingfile!='')) {
	$file_link=$existingfile;
	$path_parts = pathinfo($file_link);
	$fileext = strtolower($path_parts['extension']);
  if  ($remotelink == '') {
	  $filename = strtolower($path_parts['basename']);
  }
	//if file is to be deleted need to clear the record filename so a new file can be put into place with the existing title,description
	if((isset($_POST['delete_file']) AND $_POST['delete_file'] != '')or(isset($_POST['delete_file2']) AND $_POST['delete_file2'] != '')) {
		$filename="";
		$file_link="";
		$fileext="";
	}
	$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET 
						`extension` = '$fileext', 
						`filename` = '$filename', 
						`link`='$file_link' 
						WHERE `file_id` = '$file_id' 
						AND `page_id` = '$page_id'");
}

// Update row
$database->query("UPDATE ".TABLE_PREFIX."mod_download_gallery_files SET 
							`title` = '$title', 
							`link` = '$file_link', 
							`group_id` = '$group', 
							`description` = '$description', 
							`active` = '$active',
							`modified_when` = '".time()."', 
							`modified_by` = '".$admin->get_user_id()."' 
							WHERE `file_id` = '$file_id' AND `page_id` = '$page_id'");

/**
	REORDER
*/

if($group==0){
	
	// apply special id null reorder handling (load extern functions)
	require('functions.php');
	reorder_id_null_group(TABLE_PREFIX."mod_download_gallery_files", $section_id);
	
} else {

	// Include the ordering class
	require(WB_PATH.'/framework/class.order.php');			
	// Initialize order object 
	$order = new order(TABLE_PREFIX."mod_download_gallery_files", 'position', 'file_id', 'group_id');
	// reorder all groups in this group_id
	$order->clean( $group );   
}
	
// Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$id);
} else {
	if((isset($_POST['delete_file']) AND $_POST['delete_file'] != '')or(isset($_POST['delete_file2']) AND $_POST['delete_file2'] != '')) {
		$admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$file_id);
	} else {
		$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
	}
}

// Print admin footer
$admin->print_footer();

?>