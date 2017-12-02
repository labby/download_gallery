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

//	Remove all table entries from the search-table and drop the module tables.
$database->simple_query("DELETE FROM `".TABLE_PREFIX."search` WHERE `name` = 'module' AND `value` = 'download_gallery'");
$database->simple_query("DELETE FROM `".TABLE_PREFIX."search` WHERE `extra` = 'download_gallery'");
LEPTON_handle::drop_table("mod_download_gallery_files");
LEPTON_handle::drop_table("mod_download_gallery_settings");
LEPTON_handle::drop_table("mod_download_gallery_groups");
LEPTON_handle::drop_table("mod_download_gallery_file_ext");

//	Remove the download_gallery folder in the media dir
LEPTON_handle::delete_obsolete_directories(MEDIA_DIRECTORY.'/download_gallery');

?>