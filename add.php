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
$page_id = (int) $page_id;
$section_id = (int) $section_id;

$header = '';
$footer = '';
$file_header = '';
$files_loop = '';
$file_footer = '';
$gloop = '';
$search_layout = '';
$gheader = '';
$gfooter = '';
init_fields($header, $footer, $file_header, $files_loop, $file_footer, $gloop, $search_layout, $gheader, $gfooter);
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_settings (section_id,page_id,header,files_loop,footer,file_header,file_footer,gloop,search_layout,gheader,gfooter)
                 VALUES ('$section_id','$page_id','$header','$files_loop','$footer','$file_header','$file_footer','$gloop','$search_layout','$gheader','$gfooter')");

$image_array	= "jpg,jpeg,jpe,jfif,gif,bmp,dib,png,tif,tiff,wmf,emf,psp";
$movie_array	= "mov,wma";
$music_array	= "mp3,mid,rmi,midi,wav,snd,au,aif,aiff,ra,ram,rm,ogg";
$docs_array	= "doc,dot";
$pres_array	= "ppa,pps,ppt";
$excel_array	= "xla,xlb,xlc,xld,xlk,xll,xlm,xls,xlt,xlv,xlw,xlxml";
$compr_array	= "arj,cab,lzh,tar,tz,zip";
$pdf_array	= "pdf";
$txt_array	= "txt,bat,ini,log";

$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','images','image.gif','$image_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','movies','movie.gif','$movie_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','music','music.gif','$music_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','documents','document.gif','$docs_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','presentations','presentation.gif','$pres_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','spreadsheets','spreadsheet.gif','$excel_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','compressions','compression.gif','$compr_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','pdf','pdf.gif','$pdf_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','text','text.gif','$txt_array')");

?>