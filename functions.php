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

if(!function_exists('microtime_float')){
	function microtime_float() {
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}
}


/**
	function: human_file_size
	
	make human readable filesize (bytes)
	
*/
if(!function_exists('human_file_size')){
	function human_file_size($bytes, $precision = 2)
	{
		$name = array('Bytes','KB','MB','GB','TB');
		
		if (!is_numeric($bytes) || $bytes < 0) 
			return false;
			
		for ($level = 0; $bytes >= 1024; $level++) 
			$bytes /= 1024;
		
		return round($bytes, $precision) . ' ' . $name[$level];
	}
}


if(!function_exists('make_dl_dir')){
	function make_dl_dir() {
	   make_dir(LEPTON_PATH.MEDIA_DIRECTORY.'/download_gallery/');
	
	   // add .htaccess file to /media/download_gallery folder if not already exist
	   if (!file_exists(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery/.htaccess')
		  or (filesize(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery/.htaccess') < 90))
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
	}
}

/**
	function: dg_change_position	
	
	$direction: up || down
	$id:		file_id || group_id
	$table:		file || group
*/
if(!function_exists('dg_change_position')){
	function dg_change_position( $direction, $id, $table = 'file')
	{
	
		global $ordering, $page_id, $section_id;
			
		$baselink = LEPTON_URL.'/modules/download_gallery/move_%s.php?page_id=%d&amp;section_id=%d&amp;';
		
		if($table == 'file'){
		
			$link = $baselink.'file_id=%d';
			
			// work out if we will use move_up or move_down file
			if($ordering == 'ASC')		
				$move_direction = ($direction == 'up') 	? 'up' : 'down';
			elseif($ordering == 'DESC')
				$move_direction = ($direction == 'down')? 'up' : 'down';
					
		}elseif($table == 'group'){
		
			$link = $baselink.'group_id=%d';
			$move_direction = $direction;
			
		}else return;	
			
		return(sprintf($link, $move_direction, $page_id, $section_id, $id));
	}
}


/**
	special reorder for stupid group_id '0'
	this function was introduced to handle file positions correctly
*/
if(!function_exists('reorder_id_null_group')){
	
	function reorder_id_null_group($table, $section_id) 
	{
		global $database;
		// Loop through all records and give new order
		$get_all = $database->query(sprintf("SELECT * FROM `%s` WHERE `group_id` = '0' AND `section_id` = '%d' ORDER BY `position` ASC", $table, $section_id));
		if($get_all->numRows() > 0) {
			$i = 0;
		
			while($row = $get_all->fetchRow()) {
				$i++;
				// Update each row with new order
				$database->query("UPDATE `".$table."` SET `position` = '$i' WHERE `file_id` = '".$row['file_id']."' AND `group_id` = '0' AND `section_id` = '".$section_id."'");				
				
			}	
				
		} else return true;
		
	}
	
}

?>