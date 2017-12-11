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

//get instance of admin object and own module class
$admin = new LEPTON_admin('Pages', 'pages_modify');
$DGTEXT = download_gallery::getInstance()->language;

// include core functions to edit the optional module CSS files (frontend.css, backend.css)
LEPTON_handle::include_files('/framework/summary.module_edit_css.php');

	
// Get General Settings
$dg_settings = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_settings WHERE section_id = '$section_id' and page_id = '$page_id'" ,
	true,
	$dg_settings,
	false
);

// List Extension types
$dg_file_ext = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE section_id = '$section_id' and page_id = '$page_id'" ,
	true,
	$dg_file_ext,
	true
);

//get edit_area function and get content of template
require_once LEPTON_PATH.'/modules/edit_area/register.php';

$edit_area = registerEditArea('template_area', 'html');

if(file_exists(LEPTON_PATH.'/templates/'.DEFAULT_TEMPLATE.'/frontend/download_gallery/view.lte')) {
	$template_content = file_get_contents(LEPTON_PATH.'/templates/'.DEFAULT_TEMPLATE.'/frontend/download_gallery/view.lte');
	$template_file = LEPTON_PATH.'/templates/'.DEFAULT_TEMPLATE.'/frontend/download_gallery/view.lte';
} else {
	$template_content = file_get_contents(LEPTON_PATH.'/modules/download_gallery/templates/view.lte');
	$template_file = LEPTON_PATH.'/modules/download_gallery/templates/view.lte';
}


$data = array(
	'MOD_DG' 	=> $DGTEXT,
	'action_url'=>	LEPTON_URL."/modules/download_gallery/save_settings.php",
	'section_id'=>	$section_id,     
	'page_id'	=>	$page_id, 
	'dg_settings' 	=> $dg_settings,
	'dg_file_ext' 	=> $dg_file_ext,
	'edit_area' => $edit_area,		
	'template_content' => $template_content,
	'template_file' => $template_file,
	'edit_module_css'	=> edit_module_css('download_gallery')
	);

/**	
 *	get the template-engine.
 */
$oTwig = lib_twig_box::getInstance();
$oTwig->registerModule('download_gallery');
	
echo $oTwig->render( 
	"@download_gallery/modify_settings.lte",	//	template-filename
	$data							//	template-data
);

// Print admin footer
$admin->print_footer();
?>