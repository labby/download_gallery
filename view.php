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

$oDG = download_gallery::getInstance();
$oDG->init_section( $page_id, $section_id );
require_once LEPTON_PATH.'/modules/download_gallery/info.php';

// get file extension for each file
foreach ($oDG->dg_files as &$temp) {
/*
* Loop through all files to get values for view.lte
*/	
	if($temp['link'] != $temp['filename'] )
	{ //get filesize and icons of internal files
		$file_image = $oDG->get_file_extension($temp['extension']);
		$temp['file_ext'] = '<img src="'.LEPTON_URL.'/modules/download_gallery/images/'.$file_image.' " />';
		
		$temp['size'] = $oDG->get_file_size($temp['link'], $oDG->dg_settings['file_size_decimals']);
		$temp['link'] = LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$temp['file_id'].'&amp;id='.$temp['modified_when'];
	} else 
	{  //get filesize and icons of external files
		$get_extern_icon = strtolower(substr( strrchr($temp['filename'],'.'),1));
		$file_image = $oDG->get_file_extension($get_extern_icon);
		$temp['file_ext'] = '<img src="'.LEPTON_URL.'/modules/download_gallery/images/'.$file_image.'" />';   
		
		$temp['size'] = $oDG->get_external_file_size( $temp['link'], $oDG->dg_settings['file_size_decimals']);
		$temp['link'] = LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$temp['file_id'].'&amp;id='.$temp['modified_when'];
	}
}

// data for twig template engine	
$data = array(
	'addon' 	=> $oDG,
	'dateformat'=> str_replace(' ','/', DEFAULT_DATE_FORMAT),
	'addon_name' 	=> $module_name,
	
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
	"@download_gallery/view.lte",	//	template-filename
	$data							//	template-data
);	

?>