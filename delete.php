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
  
// prevent this file from being accessed directly
if (!defined('LEPTON_PATH')) die(header('Location: index.php'));

// STEP 0:	initialize some variables
$page_id = (int) $page_id;
$section_id = (int) $section_id;
$fname = '';
$ext = '';

//STEP 1:	Execute query
$query_files = $database->query("SELECT * FROM " . TABLE_PREFIX . "mod_download_gallery_files WHERE section_id = '$section_id'");

//STEP 2:	Check if query has rows
if($query_files->numRows() > 0) {

	//STEP 3:	For each file in the page, delete it from the gallery.
	while($fdetails = $query_files->fetchRow()) {
		$file_id= $fdetails['file_id'];
		$fname = $fdetails['filename'];
		$ext   = $fdetails['extension'];
		//check for multiple evtries using the same file name
		$query_duplicates = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE filename = '$fname' and extension='$ext'");
		$dups=$query_duplicates->numRows();
		//only delete the file if there is 1 database entry (not used on multiple sections)
		if($dups==1){
			$file = LEPTON_PATH.MEDIA_DIRECTORY.'/download_gallery/' . $fname;
			if(file_exists($file) AND is_writable($file)) { 
				unlink($file);
			}
		}
		//delete file database entry 
		$database->query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE file_id = '$file_id' LIMIT 1");
	}
}
//echo "delete $fname";

// STEP 4:	Also delete the table entries
$database->query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE section_id = '$section_id'");
$database->query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_settings WHERE section_id = '$section_id'");
$database->query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE section_id = '$section_id'");
$database->query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_group WHERE section_id = '$section_id'");

?>