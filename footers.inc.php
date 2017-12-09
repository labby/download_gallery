<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Aldus, erpe
 *  @copyright		2010-2018 Aldus, erpe
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

$show_search = $database->get_one("SELECT search_filter from ".TABLE_PREFIX."mod_download_gallery_settings where page_id = ".$page_id." ");
if ($show_search == 1 ) {
	$mod_footers = array(
		'frontend' => array(
			'js' => array(
				 "modules/download_gallery/js/jquery.filtertable.min.js"	
			)
		)
	);
}
