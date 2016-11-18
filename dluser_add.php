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

// Validation:		Check if details are correct. If not navigate to main.
if(!isset($_GET['sid']) OR !is_numeric($_GET['sid'])) {
	header("Location: ".LEPTON_URL."/pages/");
} else {
	$section_id = (int) $_GET['sid'];
	$page_id = (int) $_GET['pid'];
	define('SECTION_ID', $section_id);
}

// STEP 1:			Query for page id
$query_page = $database->query("SELECT parent,page_title,menu_title,keywords,description,visibility FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
if($query_page->numRows() == 0) {
	header('Location: '.LEPTON_URL.'/pages/');
} else {
	$page = $query_page->fetchRow();
	// Required page details
	define('PAGE_CONTENT', LEPTON_PATH.'/modules/download_gallery/dluser_page.php');
	// Include index (wrapper) file
	require(LEPTON_PATH.'/index.php');
}

?>