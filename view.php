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



// data for twig template engine	
$data = array(
	'addon' 	=> $oDG,
	'dg_link' 	=> LEPTON_URL.'/modules/download_gallery/dlc.php',	
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