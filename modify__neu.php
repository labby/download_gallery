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

//get instance of own module class
$MOD_DOWNLOAD_GALLERY = download_gallery::getInstance()->language;

$MODULE_URL 	= LEPTON_URL.'/modules/download_gallery';
$DG_ICONS 		= $MODULE_URL.'/images';
$MODULE_PREFIX = TABLE_PREFIX."mod_download_gallery";

// Include DownloadGallery functions file
require_once('info.php');
require_once('functions.php');

//delete empty records
$database->simple_query(sprintf("DELETE FROM `%s`  WHERE `page_id` = '%d' and section_id = '%d' and title=''", $MODULE_PREFIX."_files", $page_id, $section_id ));
$database->simple_query(sprintf("DELETE FROM `%s`  WHERE page_id = '%d' and section_id = '%d' and title=''", $MODULE_PREFIX."_groups", $page_id, $section_id ));

// Get settings
$query_settings = $database->query("SELECT * FROM ".$MODULE_PREFIX."_settings WHERE section_id = '$section_id'");
$settings 		= $query_settings->fetchRow();

$extordering	= $settings['extordering'];
$orderkey		= $settings['ordering'];

// ordering ASC | DESC
$ordering = ($orderkey == '0' || $orderkey == '2') ? 'ASC' : 'DESC';
// order by
$orderby = ($orderkey == '2' || $orderkey == '3') ? 'title' : 'position';

///set the extension order, but  position order overrides
if ($extordering == 0 && $orderby != "position"){
	$extorder=" extension ASC, ";
}elseif($extordering == 1 && $orderby != "position"){
	$extorder=" extension DESC,";
}else{
	$extorder = NULL;
}


/**

	GROUPS AND FILES Array
	
	we will buid a multidimensional array first
	and then we will pass the values through a "template"
*/
$list = array();

/*
	prepend "Group 0" for files which are not part of a particular group
	note that this is NOT a "regualar group" and we will need a lot of
	work around to handle this "pseudo group"
*/
$list[0] = array(
					'group_id' => 0,
					'{GROUP_ID}' => 0,
					'{GROUP_NAME}'	=> $TEXT['GROUP'].' "'.$TEXT['NONE'].'"',
					'{COUNT_FILES}' => $database->get_one(sprintf("SELECT COUNT(`file_id`) FROM `%s` WHERE `group_id`=%s AND `section_id`=%s ",	$MODULE_PREFIX."_files", 0, $section_id)),
					'{ROW_COLOR}' => 'nogroup',
					'group_active'	=> ''
			
			);

$file_link_pattern = LEPTON_URL.'/modules/download_gallery/%s.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;file_id=%d&amp;leptoken='.get_leptoken();
$group_link_pattern = LEPTON_URL.'/modules/download_gallery/%s.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;group_id=%d';
$add_file ='<a href="'.$MODULE_URL.'/add_file.php?page_id='.$page_id.'&amp;section_id='.$section_id.'&amp;group_id=%d" title="'.$TEXT['ADD'].' '.$TEXT['FILE'].'">
			<img src="'.$DG_ICONS.'/add_child.png" alt="" />
			</a>';
			
/**
		outer-LOOP (get GROUPS)
*/
$groups_query = sprintf("SELECT * FROM `%s` WHERE `section_id` = '%d' ORDER BY `position` %s", $MODULE_PREFIX."_groups", $section_id, 'ASC');
							
$query_groups = $database->query($groups_query);

if( $query_groups->numRows() >0 ){
	
	while($group = $query_groups->fetchRow()){	

		$list[$group['group_id']] = array(
			// key => value
			'group_id' 		=>	intval($group['group_id']),
			'group_active'	=> 	intval($group['active']),
			'{GROUP_NAME}' 	=>	$group['title'],
			'{GROUP_ID}' 	=>	intval($group['group_id']),
			'{ADD_FILE}' 	=>	sprintf($add_file, $group['group_id']),		
			'{ROW_COLOR}'	=> 	($group['position']%2)? 'even' : 'odd',	
			'{GROUP_DD}' 	=> intval($group['group_id'] + 100000), // needed for Drag'n'Drop
			'{GROUP_POS}'	=> intval($group['position']), // debug purpose only
			'{STATUS_ICON}'	=> sprintf('<img src="%s/group_status_%d.gif" alt="[%s]" title="%s" />', $DG_ICONS, $group['active'], $group['active'],
								$TEXT['GROUP'].' '.$TEXT['ACTIVE'].': '.($group['active'] == 1 ? $TEXT['YES'] : $TEXT['NO'])),		
			'{STATUS_URI}'	=> sprintf($MODULE_URL.'/change_active_status.php?page_id='.$page_id.'&group_id='.$group['group_id'].'&status=%d', $group['active']),		
			'{MODIFY_GROUP_URI}' 	=> sprintf($group_link_pattern, 'modify_group', $group['group_id']),
			'{DELETE_GROUP_URI}' 	=> sprintf($group_link_pattern, 'delete_group', $group['group_id']),				
			'{MOVE_UP_ICON}' 		=> ($group['position'] <= 1) ? '<img src="'.$DG_ICONS.'/empty.gif" border="0" alt="[]" />' 
						: '<a href="'.dg_change_position('up', $group['group_id'], 'group').'" title="'.$TEXT['MOVE_UP'].'"> <img src="'.$DG_ICONS.'/up_16.png" border="0" alt="[move_up]" /> </a>',
			
			// move_up_down dg_change_position() [functions.php]
			'{MOVE_DOWN_ICON}' 		=> ($group['position'] != $query_groups->numRows())-1 ? '<img src="'.$DG_ICONS.'/empty.gif" border="0" alt="[]" />' 
						: '<a href="'.dg_change_position('down', $group['group_id'], 'group').'" title="'.$TEXT['MOVE_DOWN'].'"> <img src="'.$DG_ICONS.'/down_16.png" border="0" alt="[move_down]" /> </a>',				
			
			'{COUNT_FILES}' 		=> $database->get_one(sprintf("SELECT COUNT(`file_id`) FROM `%s` WHERE `group_id`=%s ",	$MODULE_PREFIX."_files",	$group['group_id'])),				
		);
	
	} // endwhile ($group)
	
}else{

	$groups_found = false;
	
}

/**
	inner-LOOP (get FILES)
*/

$files_query = sprintf("SELECT * FROM `%s` WHERE section_id = '%d' ORDER BY %s %s %s", 
						$MODULE_PREFIX."_files",$section_id,$extorder,$orderby,$ordering);
						
$query_files = $database->query($files_query);

while($file = $query_files->fetchRow()) {
	// work out {FILE_UP} | {FILE_DOWN} ICON & Link
	$move_up_down = '<a href="%s" title="%s"><img src="'.$DG_ICONS.'/%s_dg.png" border="0" alt="%s" /></a>';	
	
	// workout {EXT_ICON}
	$unknown_icon = '<img src="'.$DG_ICONS.'/unknown.gif" alt="[unknown.gif]" />';	
	if($ext_icon = $database->get_one(sprintf("SELECT `file_image` FROM `%s` WHERE FIND_IN_SET( '%s', `extensions` ) >0 ", $MODULE_PREFIX."_file_ext", $file['extension']))){
		$ext_icon = str_replace('unknown.gif', $ext_icon, $unknown_icon);	
	}else	
		$ext_icon = $unknown_icon;					
	
	$list[$file['group_id']]['files'][$file['file_id']] = array(
		
		// key => value
		'group_id' 		=> intval($file['group_id']),
		'{GROUP_NAME}'	=> $list[$file['group_id']]['{GROUP_NAME}'],
		'{FILE_NAME}'	=> stripslashes($file['title']),
		'{FILE_POS}'	=> intval($file['position']), // debug purpose only
		'{FILE_ID}'		=> intval($file['file_id']),
		'{DL_COUNT}'	=> intval($file['dlcount']),
		'{FILE_SIZE}'	=> human_file_size(intval($file['size'])) ,
		'{EXT_ICON}'	=> $ext_icon,
		'{EXT_SUFFIX}'	=> '*.'.strtoupper($file['extension']),
		'{STATUS_ICON}'	=> sprintf('<img src="'.$DG_ICONS.'/status_%d_%d.gif" alt="[%s]" title="%s"/>', 
							$list[$file['group_id']]['group_active'], $file['active'], $file['active'], 
							$TEXT['FILE'].' '.$TEXT['ACTIVE'].': '.($file['active'] == 1 ? $TEXT['YES'] : $TEXT['NO'])),
		'{STATUS_URI}'	=> sprintf($MODULE_URL.'/change_active_status.php?page_id='.$page_id.'&file_id='.$file['file_id'].'&status=%d', $file['active']),
		'{F_EXT}'		=> '*.'.strtoupper($file['extension']),
		'{MODIFY_FILE_URI}' => sprintf($file_link_pattern, 'modify_file', $file['file_id']),
		'{DELETE_FILE_URI}' => "javascript: confirm_link('".$TEXT['ARE_YOU_SURE']."', '".sprintf($file_link_pattern, 'delete_file', $file['file_id'])."')",
		
		// move_up_down dg_change_position() [find in: 'functions.php']
		'{MOVE_UP}'	=> ($file['position'] != $list[$file['group_id']]['{COUNT_FILES}'] && $orderby == "position") 
							? sprintf($move_up_down, dg_change_position('down', $file['file_id']), $TEXT['MOVE_DOWN'], 'down', '\/') 
							: '<img src="'.$DG_ICONS.'/empty.gif" border="0" alt="[]" />',
							
		'{MOVE_DOWN}'=> ($file['position'] != 1 && $orderby == "position") 
							? sprintf($move_up_down, dg_change_position('up', $file['file_id']), $TEXT['MOVE_UP'], 'up', '/\\')
							: '<img src="'.$DG_ICONS.'/empty.gif" border="0" alt="[]" />'
	);			    	
	
} // endwhile ($file)		

// data for twig template engine	
$data = array(
	'MOD_DG' 	=> $MOD_DOWNLOAD_GALLERY,
	'addon' 	=> $module_name,
	'header'	=> get_menu_title($page_id),
	'module_url'=> $MODULE_URL,
	'action_url'=> LEPTON_URL."/modules/download_gallery/save_group.php",
	'section_id'=> $section_id,     
	'page_id'	=> $page_id, 
	'icons'		=> $DG_ICONS,
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
?>