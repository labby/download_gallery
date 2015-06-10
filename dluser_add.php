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

// Include config file
require('../../config.php');

// Validation:		Check if details are correct. If not navigate to main.
if(!isset($_GET['sid']) OR !is_numeric($_GET['sid'])) {
	header("Location: ".LEPTON_URL."/pages/");
} else {
	$section_id = (int) $_GET['sid'];
	$page_id = (int) $_GET['pid'];
	define('SECTION_ID', $section_id);
}

// Include database class
if(!defined('DATABASE_CLASS_LOADED')) {
	require(LEPTON_PATH.'/framework/class.database.php');
}
$database = new database();

// STEP 1:			Query for page id
$query_page = $database->query("SELECT parent,page_title,menu_title,keywords,description,visibility FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
if($query_page->numRows() == 0) {
	header('Location: '.LEPTON_URL.'/pages/');
} else {
	$page = $query_page->fetchRow();
	// Required page details
	define('PAGE_CONTENT', LEPTON_PATH.'/modules/download_gallery/dluser_page.php');
	// Include index (wrapper) file
	require(LEPTON_PATH.'/index.php');
}

?>