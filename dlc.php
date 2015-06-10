<?php

/*
	 Website Baker Module: download_gallery
	 dlc.php delivers the selected file, if the user has permissions, and increments the download counter
	 For more info see info.php
*/
 
require('../../config.php');
require(WB_PATH.'/framework/functions.php');
require(WB_PATH.'/framework/class.wb.php');

// check if module language file exists for the language set by the user (e.g. DE, EN)
if(!file_exists(WB_PATH .'/modules/download_gallery/languages/' .LANGUAGE .'.php')) 
		require_once(WB_PATH .'/modules/download_gallery/languages/EN.php');
else 	require_once(WB_PATH .'/modules/download_gallery/languages/' .LANGUAGE .'.php');


$file = ''; 
$dlcount = '';
if(!isset($_GET['file']) || !is_numeric($_GET['file'])) {

	header('Location: ../index.php');
	
}else{

	$file = intval($_GET['file']);
	
}
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	
	header('Location: ../index.php');
	
}else{

	$prove = intval($_GET['id']);
	
}

/**
	Query File
*/
$query_files = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE `file_id` = '$file' AND `modified_when` = '$prove'");
if ($query_files->numRows()==1) {

	$fetch_file = $query_files->fetchRow();
	
}else{

	header('Location: ../index.php');
	
}

$query_page=$database->query("SELECT * FROM `".TABLE_PREFIX."pages` WHERE `page_id` = '".$fetch_file['page_id']."'");
$page_info=$query_page->fetchRow();

// check download permissions:
$dl_allowed = false;
if ($page_info['visibility'] == 'public' || $page_info['visibility']=="hidden") {
	
	$dl_allowed = true;
	
}
	
if (!$dl_allowed) {
	if ((isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] != "" && is_numeric($_SESSION['USER_ID']))
	&& ($page_info['visibility']=="registered" ||  $page_info['visibility']=="private")) {
		$groups = explode(",", $page_info['viewing_groups']);
		foreach (split(",", $_SESSION['GROUPS_ID']) as $cur_group_id) {
			if (in_array($cur_group_id, $groups)) {
				$dl_allowed = true;
			}
		}
	}
}

if ($dl_allowed) {	
    // increment download counter:
	$dlcount = $fetch_file['dlcount']+1;
	$queryu="UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET `dlcount` = '$dlcount' WHERE `file_id` = '$file'";
	$database->query($queryu);

	// deliver the file:
	$orgfile = $fetch_file['link'];
	header('Location: '.$orgfile);
	
} else {

	echo "No access!";
	
}
?>