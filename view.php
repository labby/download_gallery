<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
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

include_once(LEPTON_PATH.'/modules/download_gallery/functions.php');

/**
 *	Language
 */
$lang = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($lang) ? (dirname(__FILE__))."/languages/EN.php" : $lang );

// For the curiousity: How fast do we are?
$time_start = microtime_float();
echo "<!-- start download gallery start -->\n";

// create shortcut for db queries 
$DG_PREFIX = TABLE_PREFIX."mod_download_gallery";

// Get settings
$query_settings = $database->query("SELECT * FROM `".$DG_PREFIX."_settings` WHERE `section_id` = '$section_id'");
if($query_settings->numRows() > 0)	$settings = $query_settings->fetchRow();

// replace the following settings to some more readable ones
$settings['group_header'] 	= $settings['gheader'];
$settings['group_loop'] 	= $settings['gloop'];
$settings['group_footer'] 	= $settings['gfooter'];

// ordering ASC | DESC
$ordering = ($settings['ordering'] == '0' || $settings['ordering'] == '2') ? 'ASC' : 'DESC';
// order by
$orderby = $DG_PREFIX.'_files.'.($settings['ordering'] == '2' || $settings['ordering'] == '3') ? 'title' : 'position';


// init vars
$adtitle = $adchanged = $adsize = $adcount = $adpos = $dlsearch	= $searchfor = $sort = "";
$checkfiles = false;
$position 	= 0;
$search_num = 0;

/*
	SORTING
	=======
	there may be up to 10 different buttons
	check which one the user pressed, if any
	(we do not need to check the search button, this is done by the searchfor field)
*/

if ((isset($_POST['NB']))  && (isset($_POST['NP'])) && (is_numeric($_POST['NP'])) && ($_POST['NP'] >= 0)) {
	// go to next page
	$position = intval($_POST['NP']); // start point (number of first entry to display)
	if (isset($_POST['SF']))
		$sort = $_POST['SF'];
		
} elseif ((isset($_POST['PB'])) && (isset($_POST['PP'])) && (is_numeric($_POST['PP'])) && ($_POST['PP'] >= 0)) {
	// go to prev page
	
	$position =  intval($_POST['PP']); // start point (number of first entry to display)
	if (isset($_POST['SF']))
		$sort = $_POST['SF'];
}


if (isset($_POST['R1'])) 	 $sort = $_POST['sort1'];  	// sort by title
elseif (isset($_POST['R2'])) $sort = $_POST['sort2'];  	// sort by time stamp
elseif (isset($_POST['R3'])) $sort = $_POST['sort3'];  	// sort by size
elseif (isset($_POST['R4'])) $sort = $_POST['sort4'];	// sort by number of downloads
elseif (isset($_POST['R5'])) $sort = $_POST['sort5'];  	// sort by file position (as in backend)
elseif (isset($_POST['ShowAll'])) { 
	// clean search field
        $searchfor = "";
        $position = 0;
} 
elseif ($searchfor == "") {  
	
  		$checkfiles = true;  // no sort button pressed; probably first call; we check whether files have been updated
}

// check whether sorting is active
// init sorting vars
$ctitle = 'TA'; $cchanged = 'CA'; $csize = 'SA'; $ccount = 'DA'; $cpos = 'PA';

switch ($sort){

	//title
		case 'TA':	$orderby = "title";			$ordering = "ASC";	$adtitle = 'asc';		$ctitle = 'TD';	break;	
		case 'TD':	$orderby = "title";			$ordering = "DESC";	$adtitle = 'desc';		break;	
	
	// file date
		case 'CA':	$orderby = "modified_when";	$ordering = "ASC";	$adchanged 	= 'asc';	$cchanged = 'CD';	break;	
		case 'CD':	$orderby = "modified_when";	$ordering = "DESC";	$adchanged 	= 'desc';	break;	
	
	//file size
		case 'SA': 	$orderby = "size";      	$ordering = "ASC";	$adsize = 'asc';		$csize 	= 'SD';	break;
		case 'SD':	$orderby = "size";			$ordering = "DESC";	$adsize = 'desc';		break;			
	
	// downloads count
		case 'DA':	$orderby = "dlcount";      	$ordering = "ASC";	$adcount = 'asc';		$ccount = 'DD';	break;	
		case 'DD':	$orderby = "dlcount";      	$ordering = "DESC";	$adcount = 'desc';		break;	
	
	// position	
		case 'PA':	$orderby = "position";      $ordering = "ASC";	$adpos	= 'asc';		$cpos	= 'PD';	break;
		case 'PD':	$orderby = "position";      $ordering = "DESC";	$adpos	= 'desc';		break;	
}

$orderby	= sprintf($DG_PREFIX.'_files.%s', $orderby);

// is local search active?
if(isset($_POST['searchfor'])) 
	$searchfor = htmlspecialchars($_POST['searchfor'], ENT_QUOTES);

// Get total number of available download entries
$total_num = $database->query("SELECT `title` FROM `".$DG_PREFIX."_files` WHERE `section_id` = '$section_id' AND `active` = '1' AND `title` != ''")->numRows();

// Work-out if we need to add limit code to sql
$limit_sql = ($settings['files_per_page'] != 0) ? " LIMIT $position, {$settings['files_per_page']}" : "";

//Query for serach results
if ($searchfor!="") {
	
	$dlsearch= " AND (".$DG_FILES.".title LIKE '%$searchfor%' OR ".$DG_FILES.".description LIKE '%$searchfor%')";       
	$query_filter_num = $database->query("SELECT `file_id` FROM `".$DG_FILES."` WHERE `section_id` = '$section_id' AND `active` = '1' AND `title` != '' " .$dlsearch);
	$search_num = $query_filter_num->numRows();
}

// build current link, should be secure against xss:
if ((isset($_SERVER['HTTPS'])) and ($_SERVER['HTTPS']=="on")) {
        $selflink = "https://";
} else {        
        $selflink = "http://";
}
//	$selflink .= $_SERVER['SERVER_NAME']. $_SERVER['SCRIPT_NAME'];
$selflink .= $_SERVER['HTTP_HOST']. $_SERVER['SCRIPT_NAME'];
//replace search in heading with searchlayout replacing searchbox submit and reset
if($settings['search_filter'] == '1') {
        //create the search form
        $searchstart="<div class='dlsearch'>\n";
        $searchbox= "<input type='text' name='searchfor' value='$searchfor' />\n";
        $searchsubmit = '<input type="submit" value="'.$DGTEXT['SEARCHinLIST']."\" />\n";
        if ($searchfor!='') {
                $searchsubmit .= '<input type="submit" name="ShowAll" value="'.$DGTEXT['SHOW_ALL']."\" />\n";
        }
        if ($searchfor=='') {
                $searchresult= '<p>&nbsp;</p>';
        } else {
                $searchresult= str_replace(array('[SEARCHMATCH]', '[OUT_OF1]', '[OUT_OF2]', '[ITEMS]'),
                                   array($DGTEXT['SEARCHMATCH'], $DGTEXT['OUT_OF1'], $DGTEXT['OUT_OF2'], $DGTEXT['ITEMS']),
                                   "<p>[SEARCHMATCH] <b>$search_num</b> [OUT_OF1] <b>$searchfor</b> [OUT_OF2] <b>$total_num</b> [ITEMS]</p>");
        }
        $searchend="</div>\n";

	$search = $searchstart;
	$search .=str_replace(array('[SEARCHBOX]','[SEARCHSUBMIT]','[SEARCHRESULT]'), array($searchbox, $searchsubmit, $searchresult), $settings['search_layout']);
	$search .=$searchend;
} else {
	$search=str_replace(array('[SEARCHBOX]','[SEARCHSUBMIT]','[SEARCHRESULT]'), array('', '', ''), $settings['search_layout']);
}

// Create previous and next links
if($settings['files_per_page'] != 0) {
	$prevnext = true;
	
	if ($search_num == 0)
		$view_num = $total_num;
	else
		$view_num = $search_num;
		
	$pn_button_pattern = '<input type="hidden" name="NP" value="%d" /><button class="mod_download_gallery_btn_f" type="submit" name="%s">%s</button>';
	
	 // I am not sure whether someone will need NEXT entry and PREVIOUS entry at all, but it was already here:
	$previous_link 		= ($position > 0) ? sprintf($pn_button_pattern,'PP',($position-1),'PB', "&lt;&lt; ".$TEXT['PREVIOUS']) :'';
	$previous_page_link = ($position > 0) ? sprintf($pn_button_pattern,'PP',($position-$settings['files_per_page']),'PB', "&lt;&lt; ".$TEXT['PREVIOUS_PAGE']):'';
	$next_link 			= ($position+$settings['files_per_page'] >= $view_num) ? '' : sprintf($pn_button_pattern,'NP',($position+1),'NB', $TEXT['NEXT']." &gt;&gt;");
	$next_page_link 	= ($position+$settings['files_per_page'] >= $view_num) ? '' : sprintf($pn_button_pattern,'NP',($position+$settings['files_per_page']),'NB', $TEXT['NEXT_PAGE']." &gt;&gt;");
 	
	// check whether less entries available than allowed
	$num_of = ($position+$settings['files_per_page'] > $view_num) ? $view_num : $position+$settings['files_per_page'];
 	
	$of =     ($position+1).'-'.$num_of.' '.strtolower($TEXT['OF']).' '.$view_num;
	$out_of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OUT_OF']).' '.$view_num;
	
} else {
	$prevnext = false;
}

// Print header
echo "<form name='dlg_$section_id' method='post' action='$selflink'>\n";


$header_footer_placeholders = array (

	'[DISPLAY_PREVIOUS_NEXT_LINKS]'	=>	($prevnext == false) ? 'none' : 'block',
			'[NEXT_PAGE_LINK]' 		=>	($prevnext == false) ? '' : $next_page_link, 
			'[NEXT_LINK]'			=>	($prevnext == false) ? '' : $next_link,
			'[PREVIOUS_PAGE_LINK]'	=>	($prevnext == false) ? '' : $previous_page_link,
			'[PREVIOUS_LINK]'		=>	($prevnext == false) ? '' : $previous_link,
			'[OUT_OF]'				=>	($prevnext == false) ? '' : $out_of,
			'[OF]'					=>	($prevnext == false) ? '' : $of, 
			'[SEARCH]'				=>	$search,
);
echo str_replace(array_keys($header_footer_placeholders), array_values($header_footer_placeholders),$settings['header']);

//Display message if there is no search result
// if (isset($_POST['searchfor'])and $num_files==0) echo "<br />". $DGTEXT['NOMATCH'] . ": $searchfor"."<br />";

// Loop through and show downloads
$pregroup = '';
// Query files (for this page)

$query_files = $database->query("SELECT ".$DG_PREFIX."_files.`title` AS 'file_title', ".$DG_PREFIX."_files.*
		FROM `".$DG_PREFIX."_files` 
		LEFT JOIN `".$DG_PREFIX."_groups` AS `groups`
		USING (`group_id`)
		WHERE  ".$DG_PREFIX."_files.`section_id` = '$section_id'
		AND  ".$DG_PREFIX."_files.`active` = '1'
		".$dlsearch."
		ORDER BY groups.`position`, $orderby $ordering " . $limit_sql);

$num_files = $query_files->numRows();
if($num_files > 0) {
	$counter = 0;

	// SORT BUTTONS
	$input_button_pattern = '<input type="hidden" name="sort%d" value="%s" />
							<button class="mod_download_gallery_btn_ra_f mod_download_gallery_%s_f" type="submit" name="R%d" value="%s">%s</button>';
	
	$button_placeholders = array(
		
		'[THTITLE]' 	=> sprintf($input_button_pattern, 1, $ctitle, 	$adtitle, 	1, $DGTEXT['THTITLE'], 	$DGTEXT['THTITLE']),
		'[THCHANGED]' 	=> sprintf($input_button_pattern, 2, $cchanged, $adchanged, 2, $DGTEXT['THCHANGED'], $DGTEXT['THCHANGED']),
		'[THSIZE]' 		=> sprintf($input_button_pattern, 3, $csize, 	$adsize, 	3, $DGTEXT['THSIZE'], 	$DGTEXT['THSIZE']),
		'[THCOUNT]' 	=> sprintf($input_button_pattern, 4, $ccount, 	$adcount, 	4, $DGTEXT['THCOUNT'], 	$DGTEXT['THCOUNT']),
		'[THPOSITION]' 	=> sprintf($input_button_pattern, 5, $cpos, 	$adpos, 	5, 'Pos', 				'Pos')
	);
	
	if ($sort != ""){
		$settings['file_header'] .= "<input type='hidden' name='SF' value='$sort' />";
	}
	
	// REPLACE BUTTON PLACEHOLDERS WITH VALUES
	echo str_replace(array_keys($button_placeholders), array_values($button_placeholders), $settings['file_header']);
	
	
	//--> start FILE LOOP
	while($file = $query_files->fetchRow()) {
		
		//$setting_group
		if($file['group_id'] != $pregroup){
			
			$query_groups = $database->query(sprintf("SELECT `group_id`, `title` FROM `%s` WHERE section_id = '%d' AND group_id='%d' AND active ='1'", 
												$DG_PREFIX."_groups",$section_id, $file['group_id'] ));
			
			$groups = $query_groups->fetchRow();

			$group_title = ($groups['title'] != "") ? $groups['title'] : $DGTEXT['NOGROUP'] ;
			
			if ($group_title!='') {
				 
				 echo $settings['group_header'];
				 echo str_replace('[GROUPTITLE]', $group_title, $settings['group_loop']);
				 echo $settings['group_footer'];
				 
			}
		}
		
		if ($checkfiles == true) {
			// Workout date and time of last modified file
			$file_date = "";
			$file_time = "";
			$filesize = 0;
			$unixtime = 0;
			$uri = $file['filename'];           
                
			if (strpos($uri, ':/') > 1) {
				// remote file with uri:
				//echo "<!-- DEBUG: remote file with uri -->\n";
				$treffer = 0;
				$fp = @fopen( $uri, "rb" );
				if( $fp ) {
					//echo "<!-- DEBUG: fopen success -->\n";
					$MetaData = stream_get_meta_data( $fp );
					//print_r ($MetaData);
					foreach( $MetaData['wrapper_data'] as $response ) {
						//echo "<!-- DEBUG response: $response -->\n";
						// case: redirection
						if( substr( strtolower($response), 0, 10 ) == 'location: ' ) {
							$newUri = substr( $response, 10 );
							fclose( $fp );
							break;
						}
						// case: last-modified
						elseif( substr( strtolower($response), 0, 15 ) == 'last-modified: ' ) {
							$unixtime = strtotime( substr($response, 15) );
							$treffer ++;
							if ($treffer > 1) break;
						}
						// case: Content-Length:
						elseif( substr( strtolower($response), 0, 16 ) == 'content-length: ' ) {
							$filesize = substr($response, 16);
							$treffer ++;
							if ($treffer > 1) break;
						}
					}
					@fclose( $fp );
				} 
				//else echo "<!-- DEBUG could not open uri $uri -->\n";
			} else {
                //echo "<!-- DEBUG filelink: $filelink -->\n";
                $filelink = (strpos($file['link'], '\\')===0) ? $file['link'] : LEPTON_PATH.str_replace(LEPTON_URL,'',$file['link']);
				$unixtime = filemtime($filelink);
				$filesize = filesize($filelink);
			}

			$size = human_file_size($filesize);
			$file_date = date(DATE_FORMAT, $unixtime);
			$file_time = date(TIME_FORMAT, $unixtime);

			// update file size in DB if necessary:
			if (($file['size'] != $filesize) && ($filesize > 0)) {
			
				//echo "<!-- DEBUG file size probably changed -->\n";
				$file_id = $file['file_id'];
				$database->query("UPDATE `".$DG_PREFIX."_files` SET `size` = '$filesize' WHERE `file_id` = '$file_id'");
								
			}

			// update last modified in DB if necessary:
			if (($file['modified_when'] != $unixtime) && ($unixtime > 0)) {
				//echo "<!-- DEBUG last modified probably changed -->\n";
				$file_id = $file['file_id'];
				$database->query("UPDATE `".$DG_PREFIX."_files` SET `modified_when` = '$unixtime' WHERE `file_id` = '$file_id'");
				
				if($database->is_error()) {
				  echo "<!-- DEBUG DB error: ".$database->get_error()." -->\n";
				}
				
				$file['modified_when'] = $unixtime;
				
			} else {   
			// if $checkfiles == false:
			  $file_date = date(DATE_FORMAT, $file['modified_when']);
			  $file_time = date(TIME_FORMAT, $file['modified_when']);
		  }


		} elseif ($checkfiles == false) {   
			//echo "<!-- DEBUG checkfiles == false -->\n";
			$size = human_file_size($file['size']);
			$file_date = date(DATE_FORMAT, $file['modified_when']);
			$file_time = date(TIME_FORMAT, $file['modified_when']);
		}

		// workout Extension ICON => [FTIMAGE]

		$unknown_icon = LEPTON_URL.'/modules/download_gallery/images/unknown.gif';	
		if($ext_icon = $database->get_one(sprintf("SELECT `file_image` FROM `%s` WHERE FIND_IN_SET( '%s', `extensions` ) > 0 ", 
												$DG_PREFIX."_file_ext", $file['extension']))){
			$ext_icon = str_replace('unknown.gif', $ext_icon, $unknown_icon);	
		}else	
			$ext_icon = $unknown_icon;				
				
		$dldescription = $file['description'];
		$wb->preprocess($dldescription);		
		
		// Get user data
		$users = array();		
		$user_id 						= '';
		$users[$user_id]['username'] 	= '';
		$users[$user_id]['display_name']= '';
		$users[$user_id]['email'] 		= '';
				
		$query_users = $database->query("SELECT `user_id`, `username`, `display_name`, `email` FROM ".TABLE_PREFIX."users");
		
		if($query_users->numRows() > 0) {			
			while($user = $query_users->fetchRow()) {
				// Insert user info into users array
				$user_id 						= $user['user_id'];
				$users[$user_id]['username'] 	= $user['username'];
				$users[$user_id]['display_name']= $user['display_name'];
				$users[$user_id]['email'] 		= $user['email'];
			}				
		}
		
		$uid = $file['modified_by']; 

		// Work-out the file link
		$file_placeholders = array(
			
			'[TITLE]' 		=> $file['title'], 
			'[DESCRIPTION]' => $dldescription , 
			'[LINK]' 		=> LEPTON_URL . '/modules/download_gallery/dlc.php?file=' .$file['file_id'].'&amp;id='.$file['modified_when'], 
			'[EXT]' 		=> $file['extension'], 
			'[POS]' 		=> $file['position'], 
			'[SIZE]' 		=> $size, 
			'[FTIMAGE]' 	=> $ext_icon, 
			'[DATE]' 		=>  $file_date, 
			'[TIME]' 		=> $file_time, 
			'[DL]' 			=> ($file['dlcount']=="") ? 0 : $file['dlcount'], 
			'[FID]' 		=> $file['file_id'], 			
			'[USER_ID]'		=> $file['modified_by'], 
			'[USERNAME]' 	=> $users[$uid]['username'], 
			'[DISPLAY_NAME]'=> $users[$uid]['display_name'], 
			'[EMAIL]' 		=>  $users[$uid]['email'], 
					
		);
		// REPLACE FILE PLACEHOLDERS WITH VALUES
		echo str_replace(array_keys($file_placeholders), array_values($file_placeholders), $settings['files_loop']);
		
		$pregroup = $file['group_id'];		
		$counter ++; // Increment counter
		
	} 	
	//<-- end FILE LOOP
	
	echo $settings['file_footer'];
}

// Print footer
echo str_replace(array_keys($header_footer_placeholders), array_values($header_footer_placeholders), $settings['footer']);

echo "</form>\n";

//display upload link if setting is set to allow this
if($settings['userupload'] ==1 ||	($settings['userupload']==2 && isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] != "" && is_numeric($_SESSION['USER_ID']))){
	echo '<a href="'.LEPTON_URL.'/modules/download_gallery/dluser_add.php?sid='.$section_id.'&amp;pid='.$page_id.'">'.$DGTEXT['UPLOADFILE']. '</a>';
}

//$time_end = microtime_float();
//$runtime = round($time_end - $time_start, 4);
//echo "<-- gallery generated in ".$runtime." seconds -->\n";
//echo "\n<!-- end download gallery -->\n";
?>