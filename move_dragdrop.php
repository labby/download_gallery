<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2017 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
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

if(!isset($_POST['action']) || !isset($_POST['row']) ){ 
	header( 'Location: ../../index.php' );
	
}else{	
	
	// check if user has permissions to access the module
	require_once('../../framework/class.admin.php');
	$admin = new admin('Modules', 'module_view', false, false);
	
	if (!($admin->is_authenticated() && $admin->get_permission('download_gallery', 'module'))) 
		die(header('Location: ../../index.php'));
	
	// Sanitized variables
	$action = addslashes($_POST['action']);
	// We just get the array here, and few lines below we sanitize it
	$row = $_POST['row'];	
	$sID = intval($database->get_one("SELECT `section_id` FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE `file_id` = ".intval($row[0])));
	

	$sorting = intval($database->get_one("SELECT `ordering` FROM `".TABLE_PREFIX."mod_download_gallery_settings` WHERE `section_id` = ".$sID." "));
	
	// $sort DESC? Reverse the whole array
	if($sorting == 1) $row = array_reverse($row);
	
	// This line verifies that in &action is not other text than "updatePosition", 
	// if something else is inputed (to try to HACK the DB), there will be no DB access..
	if ($action == "updatePosition"){	 
		
		$used_gID = intval( $_POST['group'] ); 
		$i = 1;		
		
		// update position for each item
		foreach ($row as $recID) {
			
			// groups has a $recID larger than 100000
			if ($recID > 100000) {
				$i = 1;			
				$used_gID = ($recID-100000);	
				continue;
	 		}
			
			$database->query("UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET `position` = ".$i.", `group_id` = ".$used_gID." WHERE `file_id` = ".$recID);
			$i ++;
			
		} //endforeach 
		
		echo '<img src="'.LEPTON_URL.'/modules/download_gallery/images/ajax-loader.gif" alt="" border="0" />';
		
	}
} 
?>