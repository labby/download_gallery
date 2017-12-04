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

// Get id
$id = '';
if(!isset($_GET['file_id']) OR !is_numeric($_GET['file_id'])) {
	if(!isset($_GET['group_id']) OR !is_numeric($_GET['group_id'])) {
		header("Location: index.php");
	} else {
		$id = (int) $_GET['group_id'];
		$id_field = 'group_id';
		$table = TABLE_PREFIX.'mod_download_gallery_groups';
		$common_field = 'section_id';

	}
} else {
	$id = (int) $_GET['file_id'];
	$id_field = 'file_id';
	$table = TABLE_PREFIX.'mod_download_gallery_files';
	$common_field = 'group_id';

}

$admin = new LEPTON_admin('Pages', 'pages_modify');

// Create new order object and reorder
$order = new LEPTON_order($table, 'position', $id_field, $common_field);

if($order->move_down($id)) {
	
	/*
		the following handling will need a rework, maybe
		(the function is loaded in any case yet, even if not needed)
	*/
	// apply special id null reorder handling (load extern functions)
	require('functions.php');
	reorder_id_null_group(TABLE_PREFIX."mod_download_gallery_files", $section_id);	


	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_error($TEXT['ERROR'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>