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

require_once(LEPTON_PATH.'/modules/download_gallery/functions.php');

$fields = array(
	'section_id'	=> $section_id,
	'page_id'		=> $page_id,
	'header'		=> '',
	'footer'		=> '',
	'files_per_page'	=> '10',
	'file_size_roundup'	=> '1',
	'file_size_decimals'=> '0',
	'ordering'		=> '3',
	'extordering'	=> '1',
	'search_filter'	=> '1'	
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
		'extensions' => "mov,mp4,wma"
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