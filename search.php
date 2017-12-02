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

function download_gallery_search($func_vars) {
	extract($func_vars, EXTR_PREFIX_ALL, 'func');
	
	// how many lines of excerpt we want to have at most
	$max_excerpt_num = $func_default_max_excerpt;
	$divider = ".";
	$result = false;
	
	// fetch all active download-gallery-items (from active groups) from this section
	$table_files = TABLE_PREFIX."mod_download_gallery_files";
	$table_groups = TABLE_PREFIX."mod_download_gallery_groups";
	$query = $func_database->query("
		SELECT f.title, f.filename, f.description, f.modified_when, f.modified_by
		FROM $table_files AS f LEFT OUTER JOIN $table_groups AS g ON f.group_id = g.group_id
		WHERE f.section_id='$func_section_id' AND f.active = '1' AND ( g.active IS NULL OR g.active = '1' )
		ORDER BY f.title ASC
	");
	// here, we call print_excerpt() only once for _all_ items
	// $res['modified_when'] and ..by'] doesn't make sense in this case.
	if($query->numRows() > 0) {
		$text = "";
		while($res = $query->fetchRow()) {
			$text .= $res['title'].$divider.$res['filename'].$divider.$res['description'].$divider;
		}
		$mod_vars = array(
			'page_link' => $func_page_link,
			'page_link_target' => "#wb_section_$func_section_id",
			'page_title' => $func_page_title,
			'page_description' => $func_page_description,
			'page_modified_when' => 0,
			'page_modified_by' => "",
			'text' => $text,
			'max_excerpt_num' => $max_excerpt_num
		);
		if(print_excerpt2($mod_vars, $func_vars)) {
			$result = true;
		}
	}
	
	// now fetch group-titles - ignore those without (active) items
	$table_groups = TABLE_PREFIX."mod_download_gallery_groups";
	$table_files = TABLE_PREFIX."mod_download_gallery_files";
	$query = $func_database->query("
		SELECT g.title
		FROM $table_groups AS g INNER JOIN $table_files AS f ON g.group_id = f.group_id
		WHERE g.section_id='$func_section_id' AND g.active = '1' AND f.active = '1'
	");
	// now call print_excerpt() for all groups, too
	if($query->numRows() > 0) {
		$text = "";
		while($res = $query->fetchRow()) {
			$text .= $res['title'].$divider;
		}
		$mod_vars = array(
			'page_link' => $func_page_link,
			'page_link_target' => "#wb_section_$func_section_id",
			'page_title' => $func_page_title,
			'page_description' => $func_page_description,
			'page_modified_when' => 0,
			'page_modified_by' => "",
			'text' => $text,
			'max_excerpt_num' => $max_excerpt_num
		);
		if(print_excerpt2($mod_vars, $func_vars)) {
			$result = true;
		}
	}	
	return $result;
}
?>
