<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2018 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
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

// create the tables
$table_fields="
	`file_id` INT NOT NULL NOT NULL auto_increment,
	`section_id` INT NOT NULL DEFAULT '0',
	`page_id` INT NOT NULL DEFAULT '0',
	`group_id` INT NOT NULL DEFAULT '0',
	`active` INT NOT NULL DEFAULT '0',
	`position` INT NOT NULL DEFAULT '0',
	`icon` VARCHAR(255) NOT NULL DEFAULT '',	
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`link` TEXT NOT NULL ,
	`filename` VARCHAR(255) NOT NULL DEFAULT '',
	`extension` VARCHAR(255) NOT NULL DEFAULT '',
	`description` TEXT NOT NULL,
	`modified_when` INT NOT NULL DEFAULT '0',
	`modified_by` INT NOT NULL DEFAULT '0',
	`dlcount` INT NOT NULL DEFAULT '0',
	`size` VARCHAR(32) NOT NULL DEFAULT '0',
	PRIMARY KEY (`file_id`)
	";
LEPTON_handle::install_table("mod_download_gallery_files", $table_fields);


$table_fields="
	`section_id` INT NOT NULL DEFAULT '0',
	`page_id` INT NOT NULL DEFAULT '0',
	`header` TEXT NOT NULL ,
	`footer` TEXT NOT NULL ,
	`search_filter` INT NOT NULL DEFAULT '1',
	`files_per_page` INT NOT NULL DEFAULT '0',
	`file_size_roundup` INT NOT NULL DEFAULT '1',
	`file_size_decimals` INT NOT NULL DEFAULT '0',
	`ordering` TINYINT(3) NOT NULL DEFAULT '0',
	`extordering` TINYINT(3) NOT NULL DEFAULT '0',
	PRIMARY KEY (`section_id`)
	";
LEPTON_handle::install_table("mod_download_gallery_settings", $table_fields);


$table_fields="
	`group_id` INT NOT NULL NOT NULL auto_increment,
	`section_id` INT NOT NULL DEFAULT '0',
	`page_id` INT NOT NULL DEFAULT '0',
	`active` INT NOT NULL DEFAULT '0',
	`position` INT NOT NULL DEFAULT '0',
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`group_id`)
	";
LEPTON_handle::install_table("mod_download_gallery_groups", $table_fields);


$table_fields="
	`fileext_id` INT NOT NULL NOT NULL auto_increment,
	`section_id` INT NOT NULL DEFAULT '0',
	`page_id` INT NOT NULL DEFAULT '0',
	`file_type` VARCHAR(255) NOT NULL DEFAULT '',
	`file_image` VARCHAR(255) NOT NULL DEFAULT '',
	`extensions` TEXT NOT NULL,
	PRIMARY KEY (`fileext_id`)
	";
LEPTON_handle::install_table("mod_download_gallery_file_ext", $table_fields);


// Insert info into the search table
// Module query info
$field_info = array();
$field_info['page_id'] = 'page_id';
$field_info['title'] = 'page_title';
$field_info['link'] = 'link';
$field_info['description'] = 'description';
$field_info['modified_when'] = 'modified_when';
$field_info['modified_by'] = 'modified_by';

$field_info = serialize($field_info);

$database->simple_query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('module', 'download_gallery', '$field_info')");

// Search query start
$query_start_code = "SELECT [TP]pages.page_id
			, [TP]pages.page_title
			, [TP]pages.link
			, [TP]pages.description
			, [TP]pages.modified_when
			, [TP]pages.modified_by 
			FROM [TP]mod_download_gallery_files, [TP]mod_download_gallery_groups,[TP]pages 
			WHERE 
			";
$database->simple_query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_start', '$query_start_code', 'download_gallery')");

// Search query body
$query_body_code = " [TP]pages.page_id = [TP]mod_download_gallery_files.page_id AND [TP]mod_download_gallery_files.title [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\' OR
			 [TP]pages.page_id = [TP]mod_download_gallery_files.page_id AND [TP]mod_download_gallery_files.description [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\' OR
			 [TP]pages.page_id = [TP]mod_download_gallery_groups.page_id AND [TP]mod_download_gallery_groups.title [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\' OR
			 [TP]pages.page_id = [TP]mod_download_gallery_files.page_id AND [TP]mod_download_gallery_files.filename [O] \'[W][STRING][W]\' AND [TP]pages.searching = \'1\'
			 ";	

$database->simple_query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_body', '$query_body_code', 'download_gallery')");

// Search query end
$query_end_code = "";	
$database->simple_query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_end', '$query_end_code', 'download_gallery')");

//Add folder for the files
require_once(LEPTON_PATH.'/framework/summary.functions.php');

// create media directory for this addon
make_dir(LEPTON_PATH.MEDIA_DIRECTORY.'/download_gallery/');
	
// add .htaccess file to /media/download_gallery folder if not already exist
if (!file_exists(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery/.htaccess') or (filesize(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery/.htaccess') < 90))
	   {
		// create a .htaccess file to prevent execution of PHP, HMTL files
		$content = '
		<Files .htaccess>
			order allow,deny
			deny from all
		</Files>
	
		<Files ~ "\.(php|pl)$">  
			ForceType text/plain
		</Files>
	
		Options -Indexes -ExecCGI
';
	
$handle = fopen(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery/.htaccess', 'w');
fwrite($handle, $content);
fclose($handle);
change_mode(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery/.htaccess', 'file');
};

?>