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

// check if this file was invoked by the expected module file
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if ($referer && strpos($referer, LEPTON_URL . '/modules/download_gallery/modify_settings.php') === false) {
	die(header('Location: ../../index.php'));
}


$DGTEXT = download_gallery::getInstance()->language;

$section_id = intval($_GET['section_id']);
$page_id = intval($_GET['page_id']);



if (isset($_GET['fileext_id'])) {
	$fileext_id = (int) $_GET['fileext_id'];
}

// Query the file extension
$query_fileext 	= $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE fileext_id = '$fileext_id' AND section_id = '$section_id' AND page_id = '$page_id'");
$extdetails 	= $query_fileext->fetchRow();

if (file_exists(LEPTON_PATH.'/templates/'.DEFAULT_THEME.'/backend/download_gallery/css/backend.css')) {
	$html_css = LEPTON_URL.'/templates/'.DEFAULT_THEME.'/backend/download_gallery/css/backend.css';
} else {
	$html_css = LEPTON_URL.'/modules/download_gallery/css/backend.css';
}

$data = array(
	'MOD_DG' 	=> $DGTEXT,
	'admin_url'	=>	ADMIN_URL,
	'html_css'=>	$html_css,
	'action_url'=>	LEPTON_URL."/modules/download_gallery/save_extsettings.php",	
	'section_id'=>	$section_id,     
	'page_id'	=>	$page_id, 
	'ext_details_id'	=> $extdetails['fileext_id'],
	'ext_details_type'	=> $extdetails['file_type'],
	'textarea_content'	=> str_replace(",",", ", $extdetails['extensions'])
	);

/**	
 *	get the template-engine.
 */
$oTwig = lib_twig_box::getInstance();
$oTwig->registerModule('download_gallery');
	
echo $oTwig->render( 
	"@download_gallery/modify_extensions.lte",	//	template-filename
	$data							//	template-data
);

?>
