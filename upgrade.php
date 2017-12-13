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


// delete obsolete columns 
$columns = array (
'use_captcha',	//no userupload
'userupload',
'files_loop',	// new output via twig
'file_header',
'file_footer',
'gheader', 
'gloop', 
'gfooter', 
'search_layout' 
);

foreach ($columns as $to_delete) {
	$database->simple_query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_settings` DROP COLUMN ".$to_delete." "); 
}

// add new column icon in files table
$database->simple_query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_files` ADD COLUMN icon VARCHAR(255) AFTER position"); 
// modify column size in files table
$database->simple_query("ALTER TABLE `".TABLE_PREFIX."mod_download_gallery_files` MODIFY size VARCHAR(32)"); 


?>