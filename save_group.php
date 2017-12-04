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
$group_id = '';
if(!isset($_POST['group_id']) OR !is_numeric($_POST['group_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$group_id = (int) $_POST['group_id'];
}

if(!isset($_POST['active']) OR !is_numeric($_POST['active'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$active = (int) $_POST['active'];
}

$admin = new LEPTON_admin('Pages', 'pages_modify');

// Vagroup_idate all fields
if($admin->get_post('title') == '') {
	$admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], LEPTON_URL.'/modules/download_gallery/modify_group.php?page_id='.$page_id.'&section_id='.$section_id.'&group_id='.$group_id);
} else {
	$title = addslashes(strip_tags($admin->get_post('title')));
}

// Update row
$database->simple_query("UPDATE ".TABLE_PREFIX."mod_download_gallery_groups SET title = '$title', active = '$active' WHERE group_id = '$group_id' and page_id = '$page_id'");

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/download_gallery/modify_group.php?page_id='.$page_id.'&section_id='.$section_id.'&group_id='.$group_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
?>