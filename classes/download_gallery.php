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
	public $dg_settings = array();
	public $dg_groups = array();
	public $dg_files = array();
	public $dg_file_ext = array();
	public static $instance;

	public function initialize() 
	{

	}
	
	public function init_section( $iPageID = 0, $iSectionID = 0 )
	{
		$database = LEPTON_database::getInstance();
	    $this->icon_image = array();
	    // start define image icons
		$this->dg_file_ext = array();
		$database->execute_query(
			"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE section_id = '".$iSectionID."' and page_id = '".$iPageID."' ORDER BY file_image " ,
			true,
			$this->dg_file_ext,
			true
		);

		//get array of icons and filetypes
		foreach ($this->dg_file_ext as &$icon) {
            $this->icon_image[ $icon['file_image'] ] = explode(",", $icon['extensions'] );
		}
		
		//get array of settings
		$this->dg_settings = array();      //reset array
		$database->execute_query(
			"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_settings WHERE section_id = '".$iSectionID."' and page_id = '".$iPageID."' " ,
			true,
			$this->dg_settings,
			false
		);	
		
		//get array of groups
		$this->dg_groups = array();      //reset array
		$database->execute_query(
			"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE section_id = '".$iSectionID."' and page_id = '".$iPageID."' " ,
			true,
			$this->dg_groups,
			true
		);	
		
		// add no group to groups array
		$this->dg_groups = array_merge(
			array(
				array (
					'group_id' => 0,
					'title'   => $this->language['NOGROUP'],
					'position' => 0
				)
			),
			$this->dg_groups
		);			

		//get array of files
		$this->dg_files = array();      //reset array
		$database->execute_query(
			"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE section_id = '".$iSectionID."' and page_id = '".$iPageID."' " ,
			true,
			$this->dg_files,
			true
		);
			
	}
	
	public function get_file_extension($file_extension ='') 
	{
		$file_image = ''; // initialize var	
		foreach ($this->icon_image as $file_key =>$file_icon_type_list) {  // get the matching file_icon
			if (in_array ($file_extension, $file_icon_type_list)) {
				$file_image = $file_key;
				break;
			}
		}
		return $file_image;
	}	
	
	public function get_file_size($file_link='',$settings_file_size_decimals='0') 
	{
        return	$this->human_file_size(filesize(str_replace(LEPTON_URL,LEPTON_PATH,$file_link)),$settings_file_size_decimals);
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
        
        return $this->human_file_size($length,$settings_file_size_decimals);	
	}	


	// make human readable filesize (bytes)
	public function human_file_size($bytes, $precision = 2)
	{
		$name = array('Bytes','KB','MB','GB','TB');
		
		if (!is_numeric($bytes) || $bytes < 0) 
			return false;
			
		for ($level = 0; $bytes >= 1024; $level++) 
			$bytes /= 1024;
		
		return round($bytes, $precision) . ' ' . $name[$level];
	}	
	
} // end of class


