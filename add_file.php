<?php

/*

 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2006, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
 
 /*
	added $_GET['group_id'] by Stefek
	we will need this value for "modify_file" where we will use it to 
	preselect the group name
 */
 
require('../../config.php');
require(LEPTON_PATH.'/modules/admin.php');				// Include WB admin wrapper script
require(LEPTON_PATH.'/framework/class.order.php');		// Include the ordering class

// STEP 0:	initialize some variables
$page_id = intval($page_id);
$section_id = intval($section_id);
$file_id = '';

if(isset($_GET['group_id']))
$group_id = intval($_GET['group_id']);
else 
$group_id = intval(0);


// Get new order
$order = new order(TABLE_PREFIX.'mod_download_gallery_files', 'position', 'file_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_files (section_id,page_id,position,active) VALUES ('$section_id','$page_id','$position','1')");

// Get the id
$file_id = $database->get_one("SELECT LAST_INSERT_ID()");

// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$file_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], LEPTON_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$file_id.'&group_id='.$group_id);
}

// Print admin footer
$admin->print_footer();

?>