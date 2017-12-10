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
$file_id = '';
$fname = '';
if(!isset($_GET['file_id']) OR !is_numeric($_GET['file_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$file_id = (int) $_GET['file_id'];
}

$admin = new LEPTON_admin('Pages', 'pages_modify');

// STEP 1:	Get post details
$query_details = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE file_id = '$file_id' and page_id = '$page_id'");
if($query_details->numRows() > 0) {
	$get_details = $query_details->fetchRow();
} else {
	$admin->print_error($TEXT['NOT_FOUND'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// get the file information
$fname = $get_details['filename'];
$ext   = $get_details['extension'];
$group_id = $database->get_one("SELECT `group_id` FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE file_id = '$file_id'");


//check for multiple evtries using the same file name
$query_duplicates = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE filename = '$fname' and extension='$ext'");
$dups=$query_duplicates->numRows();

//only delete the file if there is 1 database entry (not used on multiple sections)
if($dups==1){
	// STEP 2:	Delete any files if they exists
	$file = LEPTON_PATH.MEDIA_DIRECTORY.'/download_gallery/' . $fname;
	if(file_exists($file) AND is_writable($file)) {
		unlink($file);
	}
}
// STEP 3:	Delete post
$database->simple_query("DELETE FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE file_id = '$file_id' LIMIT 1");

// STEP 4:	Clean up ordering

/**
	REORDER
*/
if($group_id == 0){
	
	// apply special id null reorder handling (load extern functions)
	require('functions.php');
	reorder_id_null_group(TABLE_PREFIX."mod_download_gallery_files", $section_id);
	
} else {

			
	// Initialize order object 
	$order = new LEPTON_order(TABLE_PREFIX."mod_download_gallery_files", 'position', 'file_id', 'group_id');
	// reorder all groups in this group_id
	$order->clean( intval( $group_id ) );   
}
	

// STEP 5:	Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/modify_post.php?page_id='.$page_id.'&file_id='.$file_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>