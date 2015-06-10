<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
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

//	Remove all table entries from the search-table and drop the module tables.
$database->query("DELETE FROM `".TABLE_PREFIX."search` WHERE `name` = 'module' AND `value` = 'download_gallery'");
$database->query("DELETE FROM `".TABLE_PREFIX."search` WHERE `extra` = 'download_gallery'");
$database->query("DROP TABLE `".TABLE_PREFIX."mod_download_gallery_files`");
$database->query("DROP TABLE `".TABLE_PREFIX."mod_download_gallery_settings`");
$database->query("DROP TABLE `".TABLE_PREFIX."mod_download_gallery_groups`");
$database->query("DROP TABLE `".TABLE_PREFIX."mod_download_gallery_file_ext`");

//	Remove the download_gallery folder in the media dir
require_once(LEPTON_PATH.'/framework/functions/function.rm_full_dir.php');
rm_full_dir(LEPTON_PATH . MEDIA_DIRECTORY . '/download_gallery');

?>