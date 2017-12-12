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
 
class download_gallery extends LEPTON_abstract
{
	public $icon_image = array();
	public static $instance;

	public function initialize() 
	{
		global $section_id, $page_id;			
die(print_r($section_id));
		// start define image icons
		$all_icons = array();
		LEPTON_database::getInstance()->execute_query(
			"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE section_id = '".$section_id."' and page_id = '".$page_id."' ORDER BY file_image " ,
			true,
			$all_icons,
			true
		);

		//get array of icons and filetypes
		foreach ($all_icons as &$icon) {
				self::$instance->icon_image[ $icon['file_image'] ] = explode(",", $icon['extensions'] );
		}
	}
	public function get_file_extension($file_extension ='') 
	{
		$file_image = ''; // initialize var	
		foreach (self::$instance->icon_image as $file_key =>$file_icon_type_list) {  // get the matching file_icon
			if (in_array ($file_extension, $file_icon_type_list)) {
				$file_image = $file_key;
				break;
			}
		}
		return $file_image;
	}	
	
	public function get_file_size($file_link='',$settings_file_size_decimals='0') 
	{
	return	human_file_size(filesize(str_replace(LEPTON_URL,LEPTON_PATH,$file_link)),$settings_file_size_decimals);
	}

	public function get_external_file_size($file_url='',$settings_file_size_decimals='0') 
	{
		if ( !$fp = fopen( $file_url , 'r')) {
			trigger_error("Unable to open URL (".$file_url.")", E_USER_ERROR);
		}
		$meta = stream_get_meta_data($fp);
		fclose($fp);

		$length = 0;
		foreach($meta['wrapper_data'] as $temp_line)
		{
			if(0 === strpos( $temp_line, "Content-Length: " ))
			{
				$length = intval( str_replace("Content-Length: ", "", $temp_line ) ); // insert value
				
				break;
			}
		}
	return human_file_size($length,$settings_file_size_decimals);	
	}	
}


