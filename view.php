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

LEPTON_handle::include_files('/modules/download_gallery/functions.php');
$DGTEXT = download_gallery::getInstance()->language;
require_once LEPTON_PATH.'/modules/download_gallery/info.php';

// For the curiousity: How fast do we are?
$time_start = microtime_float();

// Get all settings
$dg_settings = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_settings WHERE section_id = '$section_id' and page_id = '$page_id' " ,
	true,
	$dg_settings,
	false
);

// Get all files
$all_files = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE section_id = '$section_id' and page_id = '$page_id' ORDER BY group_id, position " ,
	true,
	$all_files,
	true
);

// define image icons
$image_icons = array('jpg','jpeg','png','gif');
/**
 * erpe
		$url = 'https://cdn.pixabay.com/photo/2013/11/20/17/10/tree-213980_960_720.jpg';
if (!$fp = fopen($url, 'r')) {
    trigger_error("Unable to open URL ($url)", E_USER_ERROR);
}
$meta = stream_get_meta_data($fp);
$length = array_key_exists ( 'Content-Length:' , $meta);
fclose($fp);
die(print_r($length));
 */




// get file extension for each file
foreach ($all_files as &$temp) {
		
	if($temp['link'] != $temp['filename'] )
	{ //get filesize and icons of internal files
		$temp['file_ext'] = '<img src="'.LEPTON_URL.'/modules/download_gallery/images/'.$temp['extension'].'.gif" />';
		$temp['size'] = human_file_size(filesize(str_replace(LEPTON_URL,LEPTON_PATH,$temp['link'])),$dg_settings['file_size_decimals']);
		$temp['link'] = LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$temp['file_id'].'&amp;id='.$temp['modified_when'];
	} else 
	{  //get filesize and icons of external files
		$get_extern_icon = strtolower(substr( strrchr($temp['filename'],'.'),1));
		if (in_array ($get_extern_icon,$image_icons)) {
			$get_extern_icon = 'image';
		}
		$temp['file_ext'] = '<img src="'.LEPTON_URL.'/modules/download_gallery/images/'.$get_extern_icon.'.gif" />';
		
/**
 *  Playground Aldus
 *  0.1.0
 *
 */
// $url = 'https://cdn.pixabay.com/photo/2013/11/20/17/10/tree-213980_960_720.jpg';

if ( !$fp = fopen( $temp['link'] , 'r')) {
    trigger_error("Unable to open URL ($url)", E_USER_ERROR);
}

$meta = stream_get_meta_data($fp);
fclose($fp);

// echo LEPTON_tools::display( $meta );

$length = 0;
foreach($meta['wrapper_data'] as $temp_line)
{
    if(0 === strpos( $temp_line, "Content-Length: " ))
    {
        $length = intval( str_replace("Content-Length: ", "", $temp_line ) ); // !here
        
        break;
    }
}
// end aldus		

        
		$temp['size'] = human_file_size( $length , $dg_settings['file_size_decimals']);
		$temp['link'] = LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$temp['file_id'].'&amp;id='.$temp['modified_when'];
	}
}



// Group list
$dg_groups = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE section_id = '$section_id' and page_id = '$page_id'" ,
	true,
	$dg_groups,
	true
);

$dg_groups = array_merge(
    array(
		array (
			'group_id' => 0,
			'title'   => $DGTEXT['NOGROUP'],
			'position' => 0
		)
    ),
    $dg_groups
);


// data for twig template engine	
$data = array(
	'MOD_DG' 	=> $DGTEXT,
	'section_id'=> $section_id,	
	'all_files'	=> $all_files,
	'page_title'=> $database->get_one("SELECT page_title from ".TABLE_PREFIX."pages where page_id = ".$page_id." "),
	'dateformat'=> str_replace(' ','/', DEFAULT_DATE_FORMAT),
	'groups'=> $dg_groups,
	'settings'=> $dg_settings,	
	'addon' 	=> $module_directory,
	
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