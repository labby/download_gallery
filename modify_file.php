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
if(!isset($_GET['file_id']) || !is_numeric($_GET['file_id'])) 
{	
	header("Location: ".ADMIN_URL."/pages/index.php");
}
else {
	$file_id = intval($_GET['file_id']);
}

//get instance of admin object and own module class
$admin = new LEPTON_admin('Pages', 'pages_modify');
$DGTEXT = download_gallery::getInstance()->language;

$preselected_group = (isset($_GET['group_id']) && is_numeric($_GET['group_id'])) ? intval($_GET['group_id']) : 0;

// Get header and footer
$dg_file = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE file_id = '".$file_id."' and page_id = '".$page_id."'" ,
	true,
	$dg_file,
	false
);

// General File Information
$fname = $dg_file['filename'];
if($fname == '') {
//	$fname = 'dummy_file_name.ext';
  $remotelink = '';
} elseif ((strpos($fname, ':/') > 1)) {
	$remotelink = $fname;
	$fname = 'dummy_file_name.ext';
} else {
  $remotelink = '';
}

$file_handle = '';
if(file_exists(LEPTON_PATH.MEDIA_DIRECTORY.'/download_gallery/' .$fname ) && ($fname != '')) 
{
	$file_handle .=  '<b>'.$fname.'</b>&nbsp;&nbsp;' ;
	$file_handle .=  '<input type="checkbox" name="delete_file" id="delete_file" value="true" />'.$TEXT['DELETE'].' ' ;
}
elseif (trim($remotelink) !="")  {
	$file_handle .=  '<input type="file" name="file" />' ;
}
elseif (trim ($fname) !="")  {
	$file_handle .=  '<b><input type="hidden" name="existingfile"  value="'.$dg_file['link'].'">'.$dg_file['link'].'</b>' ;
	$file_handle .=  '<input type="checkbox" name="delete_file2" id="delete_file2" value="true" />'.$TEXT['DELETE'].' ' ;
}
else {
	$file_handle .=  '<input type="file" name="file" />' ;
}

// all directories of media_directory
$directories = directory_list(LEPTON_PATH.MEDIA_DIRECTORY);
array_push($directories,LEPTON_PATH.MEDIA_DIRECTORY);
foreach ($directories as $temp) {
	$folder_list[]= file_list($temp);
}
$file_list = array();
foreach ($folder_list as $temp) {
	foreach ($temp as $file) {
		$file_list[]= str_replace (LEPTON_PATH, LEPTON_URL,$file);
	}
}
natsort($file_list);

//get all groups
$dg_groups = array();
$database->execute_query(
	"SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE section_id = '".$section_id."' ORDER BY position ASC" ,
	true,
	$dg_groups,
	true
);

if (!defined('WYSIWYG_EDITOR') OR WYSIWYG_EDITOR=="none" OR !file_exists(LEPTON_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php')) {
	function show_wysiwyg_editor($name,$id,$content,$width,$height) {
		echo '<textarea name="'.$name.'" id="'.$id.'" style="width: '.$width.'; height: '.$height.';">'.$content.'</textarea>';
	}
} else {
	$id_list=array("content");
	require(LEPTON_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php');
}

if (empty($dg_file['title']))
	echo '<script type="text/javascript">document.getElementById("title").focus();</script>';
	
$data = array(
	'MOD_DG' 	=> $DGTEXT,
	'action_url'=>	LEPTON_URL."/modules/download_gallery/save_file.php",
	'section_id'=>	$section_id,     
	'page_id'	=>	$page_id, 
	'file_name'	=> $fname,
	'dg_file' 	=> $dg_file,
	'dg_groups' 	=> $dg_groups,	
	'file_handle' 	=> $file_handle,
	'file_list' 	=> $file_list,	
	'remote_link' 	=> $remotelink,
	'preselected_group' => $preselected_group,	
	'wysiwyg'		=>show_wysiwyg_editor("description","description",$dg_file['description'], "100%", "400",false)
	);

/**	
 *	get the template-engine.
 */
$oTwig = lib_twig_box::getInstance();
$oTwig->registerModule('download_gallery');
	
echo $oTwig->render( 
	"@download_gallery/modify_file.lte",	//	template-filename
	$data							//	template-data
);

// Print admin footer
$admin->print_footer();

?>