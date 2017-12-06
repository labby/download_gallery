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
if(!isset($_GET['group_id']) OR !is_numeric($_GET['group_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$group_id = $_GET['group_id'];
}

// load language file 
$MOD_DOWNLOAD_GALLERY = download_gallery::getInstance()->language;
$admin = new LEPTON_admin('Pages', 'pages_modify');

// Get header and footer
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE group_id = '$group_id' and page_id = '$page_id'");
$fetch_content = $query_content->fetchRow();

if (empty($fetch_content['title']))
	echo '<script type="text/javascript">document.getElementById("title").focus();</script>';

$data = array(
//	'print'=> (print_r($_SESSION["signup_error"])),
	'MOD_DG' 	=> $MOD_DOWNLOAD_GALLERY,
	'admin_url'	=>	ADMIN_URL,
	'action_url'=>	LEPTON_URL."/modules/download_gallery/save_group.php",
	'section_id'=>	$section_id,     
	'page_id'	=>	$page_id, 
	'group_id'	=> $group_id,
	'active'	=> $fetch_content['active'],
	'title'		=> stripslashes($fetch_content['title'])
	);

/**	
 *	get the template-engine.
 */
$oTwig = lib_twig_box::getInstance();
$oTwig->registerModule('download_gallery');
	
echo $oTwig->render( 
	"@download_gallery/modify_group.lte",	//	template-filename
	$data							//	template-data
);



// Print admin footer
$admin->print_footer();

?>