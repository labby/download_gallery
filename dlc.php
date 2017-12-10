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


$file = ''; 
$dlcount = '';
if(!isset($_GET['file']) || !is_numeric($_GET['file'])) {

	header('Location: ../index.php');
	
}else{

	$file = intval($_GET['file']);
	
}
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	
	header('Location: ../index.php');
	
}else{

	$prove = intval($_GET['id']);
	
}

/**
	Query File
*/
$query_files = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_download_gallery_files` WHERE `file_id` = '$file' AND `modified_when` = '$prove'");
if ($query_files->numRows()==1) 
{
	$fetch_file = $query_files->fetchRow();	
}else
{
	header('Location: ../index.php');	
}

$query_page=$database->query("SELECT * FROM `".TABLE_PREFIX."pages` WHERE `page_id` = '".$fetch_file['page_id']."'");
$page_info=$query_page->fetchRow();

// check download permissions:
$dl_allowed = false;
if ($page_info['visibility'] == 'public' || $page_info['visibility']=="hidden") 
{	
	$dl_allowed = true;	
}
	
if (!$dl_allowed) 
{
	if ((isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] != "" && is_numeric($_SESSION['USER_ID']))
	&& ($page_info['visibility']=="registered" ||  $page_info['visibility']=="private")) 
	{
		$groups = explode(",", $page_info['viewing_groups']);
		foreach (split(",", $_SESSION['GROUPS_ID']) as $cur_group_id) 
		{
			if (in_array($cur_group_id, $groups)) 
			{
				$dl_allowed = true;
			}
		}
	}
}

if ($dl_allowed) 
{	
    // increment download counter:
	$dlcount = $fetch_file['dlcount']+1;
	$query_up="UPDATE `".TABLE_PREFIX."mod_download_gallery_files` SET `dlcount` = '$dlcount' WHERE `file_id` = '$file'";
	$database->query($query_up);

	// deliver the file:
	$orgfile = $fetch_file['link'];
	header('Location: '.$orgfile);
	
} else 
{
	echo "No access!";	
}
?>