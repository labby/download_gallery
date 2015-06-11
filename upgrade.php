<?php
/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @license		GNU General Public License
 *  @license terms	see info.php of this module
 *  @platform		see info.php of this module
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'LEPTON_PATH' ) )
{
    include( LEPTON_PATH . '/framework/class.secure.php' );
} 
else
{
    $oneback = "../";
    $root    = $oneback;
    $level   = 1;
    while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) )
    {
        $root .= $oneback;
        $level += 1;
    } 
    if ( file_exists( $root . '/framework/class.secure.php' ) )
    {
        include( $root . '/framework/class.secure.php' );
    } 
    else
    {
        trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
    }
}
// end include class.secure.php

require_once(LEPTON_PATH.'/framework/summary.functions.php');

//setup styles to help id errors
echo '
<style type="text/css">
.good{
	color: green;
}
.bad{
	color: red;
}
.ok{
	color: blue;
}
.warn{
	color: yellow;
}
</style>
';

// Add column to table
$mod_dlg = "select `size` from `".TABLE_PREFIX."mod_download_gallery_files` limit 0";
if (false == $database->query($mod_dlg)) {
	//add the column
	$mod_dlg = 'ALTER TABLE `'.TABLE_PREFIX.'mod_download_gallery_files` ADD `size` INT NOT NULL DEFAULT \'0\'';
	$database->query($mod_dlg);
}


//Create new groups table
echo'<b><span class="good">Creating new table for download gallery groups</b><span><br /><br />';
$mod_dlg = 'CREATE TABLE IF NOT EXISTS `'.TABLE_PREFIX.'mod_download_gallery_groups` ( '
					 . '`group_id` INT NOT NULL AUTO_INCREMENT,'
					 . '`section_id` INT NOT NULL DEFAULT \'0\','
					 . '`page_id` INT NOT NULL DEFAULT \'0\','
					 . '`position` INT NOT NULL DEFAULT \'0\','
					 . '`active` INT NOT NULL DEFAULT \'0\','
					 . '`title` VARCHAR(255) NOT NULL DEFAULT \'\','
					 . 'PRIMARY KEY (group_id)'
					 . ' )';
$database->query($mod_dlg);

//get settings table to see what needs to be created
$settingstable=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_settings");
$settings = $settingstable->fetchRow();

// If not already there, add new fields to the existing settings table
echo'<span class="good"><b>Adding new fields to the settings table</b></span><br />';

if(!isset($settings['gfooter'])){
		if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` ADD `gfooter` TEXT NOT NULL AFTER `userupload`")) {
			echo '<span class="good">Database Field gfooter added successfully</a><br />';
		}
		echo '<span class="bad">'.mysql_error().'</span><br />';
}else{echo '<span class="ok">Database Field gfooter exists update not needed</span><br />';}


if(!isset($settings['gloop'])){
		if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` ADD `gloop` TEXT NOT NULL AFTER `userupload`")) {
			echo '<span class="good">Database Field gloop added successfully</span><br />';
		}
		echo '<span class="bad">'.mysql_error().'</span><br />';
}else{echo '<span class="ok">Database Field gloop exists update not needed</span><br />';}
		

if(!isset($settings['gheader'])){
		if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` ADD `gheader` TEXT NOT NULL AFTER `userupload`")) {
			echo '<span class="good">Database Field gheader added successfully</span><br />';
		}
		echo '<span class="bad">'.mysql_error().'</span><br />';
}else{echo '<span class="ok">Database Field gheader exists update not needed</span><br />';}
		

if(!isset($settings['search_filter'])){
		if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` ADD `search_filter` TEXT NOT NULL AFTER `gfooter`")) {
			echo '<span class="good">Database Field search_filter added successfully</span><br />';
		}
		echo '<span class="bad">'.mysql_error().'</span><br />';
}else{echo '<span class="ok">Database Field search_filter exists update not needed</span><br />';}
		

if(!isset($settings['search_layout'])){
		if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` ADD `search_layout` TEXT NOT NULL AFTER `search_filter`")) {
			echo '<span class="good">Database Field search_layout added successfully</span><br />';
		}
		echo '<span class="bad">'.mysql_error().'</span><br />';
}else{echo '<span class="ok">Database Field search_layout exists update not needed</span><br />';}


if(!isset($settings['use_captcha'])){
		if($database->query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` ADD `use_captcha` TINYINT( 3 ) NOT NULL DEFAULT '0' AFTER `search_layout`")) {
			echo '<span class="good">Database Field use_captcha added successfully</span><br />';
		}
		echo '<span class="bad">'.mysql_error().'</span><br />';
}else{echo '<span class="ok">Database Field suse_captcha exists update not needed</span><br />';}

echo"<br>";

// Insert default settings
$header = addslashes('[SEARCH]');

$footer = addslashes('<table cellpadding="0" cellspacing="0" border="0" width="98%" style="display: [DISPLAY_PREVIOUS_NEXT_LINKS]">
<tr>
<td width="35%" align="left">[PREVIOUS_PAGE_LINK]</td>
<td width="30%" align="center">[OF]</td>
<td width="35%" align="right">[NEXT_PAGE_LINK]</td>
</tr>
</table>');

$file_header = addslashes('<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td class="mod_download_gallery_line_f"> [THTITLE] </td>
<td class="mod_download_gallery_line_rightalign_f"> [THCHANGED] </td>
<td class="mod_download_gallery_line_rightalign_f"> [THSIZE] </td>
<td class="mod_download_gallery_line_rightalign_f"> [THCOUNT]  </td>
</tr>');

$files_loop = addslashes('<tr>
<td class="mod_download_gallery_line_f"><img src="[FTIMAGE]" alt="" /> <a href="[LINK]" target="dlg"><b>[TITLE]</b></a></td>
<td class="mod_download_gallery_line_rightalign_f"> [DATE]</td>
<td class="mod_download_gallery_line_rightalign_f"> [SIZE]</td>
<td class="mod_download_gallery_line_rightalign_f"> [DL] </td>
</tr>
<tr>
<td class="mod_download_gallery_line_text_f" colspan="4">[DESCRIPTION]</td>
</tr>');

$file_footer = addslashes('</table>');

$gloop = addslashes('<tr>
<td colspan="4">&nbsp;</td>
</tr>
<tr>
<td class="mod_download_gallery_dgheader_f" colspan="4">[GROUPTITLE]</td>
</tr>');

$search_layout = addslashes('[SEARCHBOX] [SEARCHSUBMIT] [SEARCHRESULT]');

$query_dates = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_settings where section_id != 0 and page_id != 0");
while($result = $query_dates->fetchRow()) {

	echo "<br /><b>Add default settings to database for downloadgallery section_id= ".$result['section_id']."</b><br />";
	$section_id = $result['section_id'];

	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `header` = '$header' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data header added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';

		
	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `footer` = '$footer' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data footer added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';
	
	
	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `file_header` = '$file_header' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data file_header added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';

		
	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `files_loop` = '$files_loop' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data files_loop added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';
	
	
	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `file_footer` = '$file_footer' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data file_footer added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';
	
	
	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `gloop` = '$gloop' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data gloop added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';

	
	if($database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_settings` SET `search_layout` = '$search_layout' WHERE `section_id` = $section_id")) {
		echo '<span class="good">Database data search layout added successfully</span> - ';
	}
	echo '<span class="bad">'.mysql_error().'</span><br />';
}

//Remove old search entries
echo"<br /><b>Remove old entries in the search table</b><br /><br />";
$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE name = 'module' AND value = 'download_gallery'");
$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE extra = 'download_gallery'");

// Add new search entries
echo"<b>Add new entries to the search table</b><br /><br />";
// Module query info
$field_info = array();
$field_info['page_id'] = 'page_id';
$field_info['title'] = 'page_title';
$field_info['link'] = 'link';
$field_info['description'] = 'description';
$field_info['modified_when'] = 'modified_when';
$field_info['modified_by'] = 'modified_by';
$field_info = serialize($field_info);

$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('module', 'download_gallery', '$field_info')");

// Search query start
$query_start_code = "SELECT [TP]pages.page_id
						  , [TP]pages.page_title
						  , [TP]pages.link
						  , [TP]pages.description
						  , [TP]pages.modified_when
						  , [TP]pages.modified_by 
					 FROM [TP]mod_download_gallery_files,[TP]mod_download_gallery_groups, [TP]pages 
					 WHERE 
					";
$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_start', '$query_start_code', 'download_gallery')");

// Search query body
$query_body_code = " [TP]pages.page_id = [TP]mod_download_gallery_files.page_id AND [TP]mod_download_gallery_files.title [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\' OR
					 [TP]pages.page_id = [TP]mod_download_gallery_files.page_id AND [TP]mod_download_gallery_files.description [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\' OR
					 [TP]pages.page_id = [TP]mod_download_gallery_groups.page_id AND [TP]mod_download_gallery_groups.title [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\' OR
					 [TP]pages.page_id = [TP]mod_download_gallery_files.page_id AND [TP]mod_download_gallery_files.filename [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\'
				   ";	
$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_body', '$query_body_code', 'download_gallery')");

// Search query end
$query_end_code = "";	
$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_end', '$query_end_code', 'download_gallery')");

// Insert blank row (there needs to be at least on row for the search to work)
$query_0=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE section_id='0' and page_id='0'");
	if($query_0->numRows() == 0) {
		$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_files (section_id,page_id) VALUES ('0', '0')");
	}
	
$query_0=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_settings` WHERE section_id='0' and page_id='0'");
	if($query_0->numRows() == 0) {
		$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_settings (section_id,page_id) VALUES ('0', '0')");
	}

$query_0=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_groups` WHERE section_id='0' and page_id='0'");
	if($query_0->numRows() == 0) {
		$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_groups (section_id,page_id) VALUES ('0', '0')");
	}

// update .htaccess file in /media/download_gallery folder 
include_once(LEPTON_PATH.'/modules/download_gallery/functions.php');
make_dl_dir();

?>