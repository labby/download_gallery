<?php
/*
	Drag'N'Drop Position
	
	This file is based on the mechanism used in Module "Members" by Chio (www.beesign.com)
	Big thanks to Ivan (CrnoGorak) for further hints and help on implementation
*/

if(!isset($_POST['action']) || !isset($_POST['row']) ){ 
	header( 'Location: ../../index.php' );
	
}else{	

	require('../../config.php');
	
	// check if user has permissions to access the Bakery module
	require_once('../../framework/class.admin.php');
	$admin = new admin('Modules', 'module_view', false, false);
	
	if (!($admin->is_authenticated() && $admin->get_permission('download_gallery', 'module'))) 
		die(header('Location: ../../index.php'));
	
	// Sanitized variables
	$action = $admin->add_slashes($_POST['action']);
	// We just get the array here, and few lines below we sanitize it
	$row = $_POST['row'];	
	$sID = intval($database->get_one("SELECT `section_id` FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE `file_id` = ".intval($row[0])));
	

	$sorting = intval($database->get_one("SELECT `ordering` FROM `".TABLE_PREFIX."mod_download_gallery_settings` WHERE `section_id` = ".$sID." "));
	
	// $sort DESC? Reverse the whole array
	if($sorting == 1) $row = array_reverse($row);
	
	// This line verifies that in &action is not other text than "updatePosition", 
	// if something else is inputed (to try to HACK the DB), there will be no DB access..
	if ($action == "updatePosition"){	 
		
		$used_gID = intval( $_POST['group'] ); 
		$i = 1;		
		
		// update position for each item
		foreach ($row as $recID) {
			
			// groups has a $recID larger than 100000
			if ($recID > 100000) {
				$i = 1;			
				$used_gID = ($recID-100000);	
				continue;
	 		}
			
			$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET `position` = ".$i.", `group_id` = ".$used_gID." WHERE `file_id` = ".$recID);
			$i ++;
			
		} //endforeach 
		
		echo '<img src="'.WB_URL.'/modules/bakery/images/ajax-loader.gif" alt="" border="0" />';
		
	}
} 
?>