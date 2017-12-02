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


$admin = new LEPTON_admin('Pages', 'pages_modify');

if(isset($_GET['group_id']))
{
	$group_id = intval($_GET['group_id']);
}
else 
{
	$group_id = 0;
}


// Get new order
$order = new LEPTON_order(TABLE_PREFIX.'mod_download_gallery_files', 'position', 'file_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$fields = array(
	'section_id'=> $section_id,
	'page_id'	=> $page_id,
	'position'	=> $position,
	'active'	=> 1
);

$database->build_and_execute(
	"insert",
	TABLE_PREFIX."mod_download_gallery_files",
	$fields
);

// Get the id
$file_id = $database->get_one("SELECT LAST_INSERT_ID()");

// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$file_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], LEPTON_URL.'/modules/download_gallery/modify_file.php?page_id='.$page_id.'&section_id='.$section_id.'&file_id='.$file_id.'&group_id='.$group_id);
}

// Print admin footer
$admin->print_footer();

?>