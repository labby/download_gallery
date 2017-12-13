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

//get instance of own module class
$oDG = download_gallery::getInstance();
$oDG->init_section( $page_id, $section_id );
LEPTON_handle::include_files('/modules/download_gallery/functions.php');
require_once LEPTON_PATH.'/modules/download_gallery/info.php';

$MODULE_URL 	= LEPTON_URL.'/modules/download_gallery';
$DG_ICONS 		= $MODULE_URL.'/images';
$MODULE_PREFIX = TABLE_PREFIX."mod_download_gallery";


//delete empty records
$database->simple_query("DELETE FROM ".$MODULE_PREFIX."_files WHERE section_id ='".$section_id."' and page_id ='".$page_id."' and title='' ");
$database->simple_query("DELETE FROM ".$MODULE_PREFIX."_groups WHERE section_id ='".$section_id."' and page_id ='".$page_id."' and title='' ");

// get file extension for each file
foreach ($oDG->dg_files as &$temp) {
/*
* Loop through all files to get values for .lte
*/	
	if($temp['link'] != $temp['filename'] )
	{ //get filesize and icons of internal files
		$file_image = $oDG->get_file_extension($temp['extension']);
		$temp['file_ext'] = '<img src="'.LEPTON_URL.'/modules/download_gallery/images/'.$file_image.' " />';
		$database->simple_query("UPDATE ".TABLE_PREFIX."mod_download_gallery_files SET `icon` = '".$temp['file_ext']."' WHERE `file_id` = '".$temp['file_id']."' ");
		
		$temp['size'] = $oDG->get_file_size($temp['link'], $oDG->dg_settings['file_size_decimals']);
		$database->simple_query("UPDATE ".TABLE_PREFIX."mod_download_gallery_files SET `size` = '".$temp['size']."' WHERE `file_id` = '".$temp['file_id']."' ");

		$temp['link'] = LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$temp['file_id'].'&amp;id='.$temp['modified_when'];
	} else 
	{  //get filesize and icons of external files
		$get_extern_icon = strtolower(substr( strrchr($temp['filename'],'.'),1));
		$file_image = $oDG->get_file_extension($get_extern_icon);
		$temp['file_ext'] = '<img src="'.LEPTON_URL.'/modules/download_gallery/images/'.$file_image.'" />';  
		$database->simple_query("UPDATE ".TABLE_PREFIX."mod_download_gallery_files SET `icon` = '".$temp['file_ext']."' WHERE `file_id` = '".$temp['file_id']."' ");
		
		
		$temp['size'] = $oDG->get_external_file_size( $temp['link'], $oDG->dg_settings['file_size_decimals']);
		$database->simple_query("UPDATE ".TABLE_PREFIX."mod_download_gallery_files SET `size` = '".$temp['size']."' WHERE `file_id` = '".$temp['file_id']."' ");
		
		$temp['link'] = LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$temp['file_id'].'&amp;id='.$temp['modified_when'];
	}
}

// data for twig template engine	
$data = array(
	'addon' => $oDG,
	'icons' => $DG_ICONS,
	'addon_name'	=> $module_name,
	'page_id'	=> $page_id,
	'section_id'	=> $section_id,	
	'module_url'	=> $MODULE_URL,
	'file_path'		=> $MODULE_URL."/modify_file.php?page_id=$page_id&section_id=$section_id",
	'active_path'	=> $MODULE_URL."/change_active_status.php",		
	'count_files'	=> count($oDG->dg_files),
	
	// data js pagination	
        'items' => 10,
        'itemsOnPage'=> 2,
        'cssStyle'=> 'compact-theme'
	);

/**	
 *	get the template-engine.
 */
$oTwig = lib_twig_box::getInstance();
$oTwig->registerModule('download_gallery');
	
echo $oTwig->render( 
	"@download_gallery/modify.lte",	//	template-filename
	$data							//	template-data
);	