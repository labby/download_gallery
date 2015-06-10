<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
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

// STEP 0:	initialize some variables
$page_id = intval($page_id);
$section_id = intval($section_id);

$header = '';
$footer = '';
$file_header = '';
$files_loop = '';
$file_footer = '';
$gloop = '';
$search_layout = '';
$gheader = '';
$gfooter = '';

//	see functions.php for details.
init_fields($header, $footer, $file_header, $files_loop, $file_footer, $gloop, $search_layout, $gheader, $gfooter);

$fields = array(
	'section_id'	=> $section_id,
	'page_id'		=> $page_id,
	'header'		=> $header,
	'files_loop'	=> $files_loop,
	'footer'		=> $footer,
	'file_header'	=> $file_header,
	'file_footer'	=> $file_footer,
	'gloop'			=> $gloop,
	'search_layout'	=> $search_layout,
	'gheader'		=> $gheader,
	'gfooter'		=> $gfooter
);

$database->build_and_execute(
	"insert",
	TABLE_PREFIX."mod_download_gallery_settings",
	$fields
);

$file_types = array(
	array(
		'file_type'	=> "images",
		'file_image' => "image.gif",
		'extensions' => "jpg,jpeg,jpe,jfif,gif,bmp,dib,png,tif,tiff,wmf,emf,psp"
	),
	array(
		'file_type'	=> "movies",
		'file_image' => "movie.gif",
		'extensions' => "mov,wma"
	),
	array(
		'file_type' => "music",
		'file_image' => "music.gif",
		'extensions' => "mp3,mid,rmi,midi,wav,snd,au,aif,aiff,ra,ram,rm,ogg"
	),
	array(
		'file_type' => "documents",
		'file_image' => "document.gif",
		'extensions' => "doc,dot"
	),
	array(
		'file_type' => "presentations",
		'file_image' => "presentation.gif",
		'extensions' => "ppa,pps,ppt"
	),
	array(
		'file_type' => "spreadsheets",
		'file_image' => "spreadsheet.gif",
		'extensions' => "xla,xlb,xlc,xld,xlk,xll,xlm,xls,xlt,xlv,xlw,xlxml"
	),
	array(
		'file_type'	=> "compressions",
		'file_image' => "compression.gif",
		'extensions' => "arj,cab,lzh,tar,tz,zip"
	),
	array(
		'file_type' => "pdf",
		'file_image' => "pdf.gif",
		'extensions' => "pdf"
	),
	array(
		'file_type' => "text",
		'file_image' => "text.gif",
		'extensions' => "txt,bat,ini,log"
	)
);

foreach($file_types as &$type) {
	$type['section_id'] = $section_id;
	$type['page_id'] = $page_id;
	
	$database->build_and_execute(
		"insert",
		TABLE_PREFIX."mod_download_gallery_file_ext",
		$type
	);
}

?>