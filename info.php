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

 
$module_directory	= 'download_gallery';
$module_name		= 'Download Gallery';
$module_function	= 'page';
$module_version		= '3.3.0';
$module_platform	= '4.0';
$module_status		= 'stable';
$module_author		= 'Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe';
$module_license		= 'GNU General Public License';
$module_description	= 'This page type is designed for making a download gallery page<br /> See <a href="'.LEPTON_URL.'/modules/download_gallery/help.php?page_id=1&amp;section_id=1">help file</a> for more information.';
$module_home		= 'http://lepton-cms.com';
$module_guid		= '5F43995D-E3E8-4E93-8E6C-121936AB7EE3'; 
?>
